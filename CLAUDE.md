# CLAUDE.md

Guidance for Claude Code when working in this repository.

## Overview

`vs-point/moneys3` — a strictly typed PHP library for the **Money S3** (Seyfor) accounting
software, which exposes a **GraphQL API** with OAuth2 auth. Framework-agnostic core plus a
thin Symfony bundle. Reads are synchronous; writes are asynchronous (import queue) and
mutations return `{ guid, isSuccess }`.

Reference docs: <https://money.cz/navod/api-v-money-s3-pro-vyvojare/> · interactive schema:
<https://s3api.api.moneys3.eu/graphql/>.

## Toolchain — always via Docker

Local PHP CLI may lack `ext-dom` (needed by PHPUnit). Run everything in the CI image:

```bash
docker run --rm -v "$(pwd):/app" -w /app vspoint/php:8.5-fpm-alpine composer install
docker run --rm -v "$(pwd):/app" -w /app vspoint/php:8.5-fpm-alpine ./vendor/bin/phpunit --testdox
docker run --rm -v "$(pwd):/app" -w /app vspoint/php:8.5-fpm-alpine ./vendor/bin/phpstan analyse --memory-limit=512M
docker run --rm -v "$(pwd):/app" -w /app vspoint/php:8.5-fpm-alpine ./vendor/bin/ecs check        # --fix to apply
```

PHPStan (level 6) and ECS must pass before committing — they run in the `analyse` CI stage
before tests.

## Architecture

- `src/GraphQL/` — the typed GraphQL builder. `InputObject::toGraphQL()` contract,
  `ValueEncoder` (PHP → GraphQL literal syntax, not JSON), `Field` selection tree,
  `QueryBuilder`, `MutationBuilder`.
- `src/Filter/` — `FieldName` interface + composable `Where` / `Order` (HotChocolate
  `{field:{op:value}}`, dotted paths nest), `FilterOperator`, `OrderDirection`. Fields are
  referenced via per-agenda `*Attribute` enums implementing `FieldName` (flexibee-style),
  e.g. `Where::field(CashJournalAttribute::year, …)` — never a raw string (`Where::path()`
  is the escape hatch). Attribute field names are verified live against `I*FilterInput`.
- `src/Auth/` — `Credentials` (client_credentials / password), `OAuth2TokenProvider`
  (caches + refreshes), `TokenProvider`.
- `src/Transport/` — `Transport` iface, `GuzzleTransport`, `GraphQLResponse`.
- `src/Hydration/Data.php` — null-safe typed reader used by response DTO `fromData()`.
- `src/Result/` — `Collection<T>`, `MutationResult` (`isSuccess`, `guid`, `assertSuccess()`).
- `src/DTO/Common/` — shared input value objects (`Address`, `PartnerAddress`, `ShortCutRef`,
  `CodeRef`, `VatRateSummary`, `StockItem`, …); `src/DTO/{Agenda}/` per-agenda inputs/responses.
- `src/Agenda/` — one service per agenda extending `AbstractAgendaService`.
- `src/Client/` — `MoneyS3Api` facade + `CloudClient` / `Client` (local) connections.
- `src/Bridge/Symfony/MoneyS3Bundle.php` — DI wiring from config.

## Key conventions

- **Every PHP file starts with `declare(strict_types=1);`** immediately after `<?php`.
- Input DTOs are `final readonly`, implement `InputObject`, and return `field => value` from
  `toGraphQL()`. **`null` values are omitted** by the encoder — that is how optional fields
  stay unset. Don't add manual null filtering.
- GraphQL enums are backed enums implementing `GraphQLEnumValue` (token = `value`). The
  encoder renders them as bare tokens.
- Mutation/query field **names and enum tokens follow the live schema**. The PDF examples
  had one PascalCase inconsistency (`CreateBankStatement`); the library normalises to
  camelCase (`createBankStatement`). If the schema disagrees, fix the name in the service.
- Two test suites: **Unit** (no network — HTTP mocked via Guzzle `MockHandler`, transport via
  `tests/Support/RecordingTransport`; assert on the rendered GraphQL document) and **Schema**
  (`tests/Schema` — validates names/enums/attribute fields against the live endpoint via
  introspection, self-skips when unreachable).
- Money/numeric fields accept `int|float|string|Brick\Math\BigNumber`; dates accept
  `string|DateTimeInterface|Brick\DateTime\LocalDate`.

## Adding an agenda

`*Attribute` enum (`FieldName`) for typed filters → input DTO (`InputObject`) → optional
response DTO (`fromData()` + `fields()`) → service
(`AbstractAgendaService`, use `queryCollection`/`queryRaw`/`mutate`) → property on
`MoneyS3Api`. Verify exact names against the schema browser.
