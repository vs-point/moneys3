# vs-point/moneys3

Striktně typovaná PHP knihovna pro komunikaci s účetním softwarem **Money S3** (Seyfor)
přes jeho **GraphQL API**. Poskytuje typovaný PHP interface pro čtení a zápis dokladů,
adresáře, skladů, deníků a dalších agend — bez ručního skládání GraphQL řetězců.

- 🔒 **Striktně typované** — vstupy (mutace) i filtry jsou PHP objekty, ne pole/řetězce.
- 🧠 **Inteligentní GraphQL builder** — typované objekty se samy serializují do platné
  GraphQL syntaxe (literály, enum tokeny, vnořené objekty, seznamy).
- 🔌 **Framework-agnostické jádro** + tenký **Symfony bundle**.
- ☁️ **Cloud i lokál** — `CloudClient` (`{domain}.api.moneys3.eu`) a `Client` (`localhost:85`).

> Money S3 API je GraphQL-based s OAuth2 autentizací. Čtení je synchronní; **zápis je
> asynchronní** — doklad vstupuje do importní fronty a mutace vrací `guid` + `isSuccess`.
> Oficiální dokumentace: <https://money.cz/navod/api-v-money-s3-pro-vyvojare/>,
> interaktivní schéma: <https://s3api.api.moneys3.eu/graphql/>.

## Požadavky

- PHP >= 8.2
- Money S3 s nainstalovaným a zakoupeným API modulem
- Vygenerovaný API klíč (Client ID + Client Secret) a Application ID (`api@money.cz`)

## Instalace

```bash
composer config repositories.moneys3 git git@git.vs-point.cz:vspoint/package/moneys3.git
composer require vs-point/moneys3:^1.0
```

Při použití Dockeru je potřeba GitLab token:

```bash
composer config --auth gitlab-token.git.vs-point.cz <token>
```

## Připojení

Knihovna nabízí dvě konkrétní třídy klienta se společným předkem `MoneyS3Api`, takže si
explicitně volíte, ke které instanci se připojujete:

| Třída | Cílová instance | Endpoint |
|-------|-----------------|----------|
| `CloudClient` | cloud | `https://{domain}.api.moneys3.eu/graphql/` |
| `Client` | lokální | `http://localhost:85/graphql/` |

### Cloud

```php
use VsPoint\MoneyS3\Auth\Credentials;
use VsPoint\MoneyS3\Client\CloudClient;

$client = new CloudClient(
    domain: 'mojefirma',
    appId: 'APP-ID-Z-MONEY',
    credentials: Credentials::clientCredentials('client-id', 'client-secret'),
);
```

### Lokální Money S3

```php
use VsPoint\MoneyS3\Auth\Credentials;
use VsPoint\MoneyS3\Client\Client;

$client = new Client(
    appId: 'APP-ID-Z-MONEY',
    credentials: Credentials::clientCredentials('client-id', 'client-secret'),
    // host: 'localhost', port: 85, secure: false  // výchozí
);
```

### Autentizace

```php
// Client Credentials (z nastavení API klíče)
Credentials::clientCredentials('client-id', 'client-secret');

// Resource Owner Password Credentials
Credentials::password('client-id', 'client-secret', 'uzivatel', 'heslo');
```

Token se získá přes `connect/token`, drží se v paměti a automaticky obnovuje před expirací.

## Použití

Klient přímo vystavuje jednu službu na agendu:

```php
$client->issuedInvoices       // faktury vystavené   — R / C / U / D
$client->receivedInvoices     // faktury přijaté      — R / C / U / D
$client->bankStatements       // bankovní doklady     — R / C / U / D
$client->cashVouchers         // pokladní doklady     — R / C / U / D
$client->receivedOrders       // objednávky přijaté   — R / C / U / D
$client->issuedOrders         // objednávky vystavené — R / C / U / D
$client->receivedSlips        // skladové příjemky    — R / C / U / D
$client->issuedSlips          // skladové výdejky     — R / C / U / D
$client->stockTakingDocuments // inventurní doklady   — R / C / U / D
$client->wages                // mzdy                 — C / U / D
$client->cashJournal          // peněžní deník        — R
$client->accountingJournal    // účetní deník         — R
$client->warehouseStocks      // skladové zásoby      — R
$client->agendas              // agendy / firmy       — R
```

### Čtení (query) — typované DTO

```php
use VsPoint\MoneyS3\DTO\Journal\CashJournalAttribute;
use VsPoint\MoneyS3\Filter\FilterOperator;
use VsPoint\MoneyS3\Filter\Order;
use VsPoint\MoneyS3\Filter\OrderDirection;
use VsPoint\MoneyS3\Filter\Where;

// Peněžní deník za rok 2026, řazený podle data sestupně, prvních 20
$result = $client->cashJournal->query(
    where: Where::field(CashJournalAttribute::year, FilterOperator::eq, 2026),
    order: Order::by(CashJournalAttribute::date, OrderDirection::desc),
    take: 20,
);

foreach ($result as $transaction) {
    echo $transaction->id . ' ' . $transaction->amountHc . ' ' . $transaction->companyName . PHP_EOL;
}
```

`$result` je `Collection<JournalTransaction>` — typovaná, iterovatelná, s `first()`,
`count()`, `isEmpty()`.

### Filtrování (`where`) — typované atributy

Pole se **nikdy nezadávají stringem** — odkazují se přes per-agenda atributový enum
implementující `FieldName` (např. `CashJournalAttribute`, `IssuedInvoiceAttribute`,
`WarehouseStockAttribute`). Díky tomu nelze odkázat na pole, které neexistuje:

```php
Where::field(CashJournalAttribute::year, FilterOperator::eq, 2026);
Where::field(CashJournalAttribute::companyIdentificationNumber, FilterOperator::eq, '01572377'); // vnořené přes tečku
Where::and(
    Where::field(CashJournalAttribute::year, FilterOperator::eq, 2026),
    Where::field(CashJournalAttribute::amount, FilterOperator::gt, 0),
);
Where::or(/* ... */);
Where::allOf([[CashJournalAttribute::year, 2026], [CashJournalAttribute::amount, 100]]); // konjunkce přes `eq`
```

Stejně tak řazení: `Order::by(CashJournalAttribute::date, OrderDirection::desc)`.

> IDE i PHPStan tak hlídají správnost: `CashJournalAttribute::yaer` se nezkompiluje.
> Názvy polí v enumech jsou navíc **ověřené proti živému schématu** (filter input typy) —
> viz [Validace proti schématu](#validace-proti-schématu).
>
> Pro pole, které zatím nemá atributový enum, existuje únikový poklop
> `Where::path('nejake.pole', …)` / `Order::path('nejake.pole', …)`.

Operátory (`FilterOperator`): `eq`, `neq`, `gt`, `gte`, `lt`, `lte`, `in`, `nin`,
`contains`, `ncontains`, `startsWith`, `nstartsWith`, `endsWith`, `nendsWith`.

### Vlastní výběr polí

Read-only agendy mají výchozí selekci pokrývající celé DTO, ale můžete si zvolit vlastní:

```php
use VsPoint\MoneyS3\GraphQL\Field;

$result = $client->warehouseStocks->query(
    fields: [
        Field::leaf('description'),
        Field::leaf('stockQuantity'),
        Field::nested('warehouse', ['code', 'name']),
    ],
    take: 100,
);
```

U dokladových agend (faktury, banka, …) je `query()` typově bezpečný „raw" čteč —
zvolíte selekci a hodnoty čtete přes `Data`:

```php
$invoices = $client->issuedInvoices->query(
    fields: [Field::leaf('documentNumber'), Field::leaf('dateOfIssue')],
    where: Where::field('variableSymbol', FilterOperator::eq, '202603'),
    take: 5,
);

foreach ($invoices as $row) {           // $row je VsPoint\MoneyS3\Hydration\Data
    echo $row->string('documentNumber') . PHP_EOL;
}
```

### Vytvoření dokladu (mutace)

```php
use VsPoint\MoneyS3\DTO\Common\Address;
use VsPoint\MoneyS3\DTO\Common\CodeRef;
use VsPoint\MoneyS3\DTO\Common\DefinitionXMLTransfer;
use VsPoint\MoneyS3\DTO\Common\PartnerAddress;
use VsPoint\MoneyS3\DTO\Common\PostalCode;
use VsPoint\MoneyS3\DTO\Common\ShortCutRef;
use VsPoint\MoneyS3\DTO\Common\VatRateSummary;
use VsPoint\MoneyS3\DTO\Invoice\InvoiceItem;
use VsPoint\MoneyS3\DTO\Invoice\IssuedInvoiceInput;
use VsPoint\MoneyS3\Enum\PriceType;

$result = $client->issuedInvoices->create(
    new IssuedInvoiceInput(
        dateOfIssue: '2026-03-02',
        dateOfTaxing: '2026-03-02',
        dateOfMaturity: '2026-03-12',
        documentNumber: '20260203',
        variableSymbol: '202603',
        description: 'Popis faktury',
        accountAssignment: new ShortCutRef('FV001'),
        vatClassification: new ShortCutRef('19Ř01,02'),
        payOn: new ShortCutRef('BAN'),
        vatRateSummaryHc: new VatRateSummary(vatRate: 21, totalWithoutVat: 100, totalVat: 50),
        partnerAddress: new PartnerAddress(
            businessAddress: new Address(
                name: 'Seyfor, a. s.',
                country: new CodeRef('CZ'),
                municipality: 'Brno',
                municipalityPostalCode: new PostalCode('60200'),
                street: 'Drobného 555/49',
            ),
            identificationNumber: '01572377',
            vatIdentificationNumber: 'CZ01572377',
        ),
        paymentMethod: 'převodem',
        items: [
            new InvoiceItem(
                description: 'Vývoj software',
                amount: 1,
                unitPriceHc: 100,
                vatRate: 21,
                priceType: PriceType::withoutVat,
            ),
        ],
    ),
    new DefinitionXMLTransfer('_FP+FV'), // definice importu
);

if ($result->isSuccess) {
    echo 'GUID v importní frontě: ' . $result->guid;
}

// nebo vyhoďte výjimku, pokud Money S3 doklad nepřijalo:
$result->assertSuccess();
```

Datová pole přijímají `string` (`"2026-03-02"`), `DateTimeInterface` i
`Brick\DateTime\LocalDate`. Peněžní/číselné hodnoty přijímají `int|float|string` i
`Brick\Math\BigNumber`.

### Skladová položka faktury

```php
use VsPoint\MoneyS3\DTO\Common\ArticleRef;
use VsPoint\MoneyS3\DTO\Common\StockItem;
use VsPoint\MoneyS3\DTO\Common\WarehouseRef;
use VsPoint\MoneyS3\DTO\Invoice\InvoiceItem;

new InvoiceItem(
    description: 'Bota goretex',
    amount: 1,
    stockItem: new StockItem(
        warehouse: new WarehouseRef(code: 'PRODEJ'),
        article: new ArticleRef(catalogue: 'K50'),
    ),
);
```

### Úprava dokladu

Update používá stejné vstupní DTO jako `create`; doklad se identifikuje přes `guid`
(případně `documentNumber`/symboly podle nastavení importu):

```php
$client->issuedInvoices->update(
    new IssuedInvoiceInput(
        guid: 'FFBD0FF4-31D0-41C2-9210-823A6DA92A47',
        description: 'Opravený popis',
    ),
);
```

### Smazání dokladu

```php
$client->issuedInvoices->delete(id: 52, year: 2025);
$client->receivedInvoices->delete(id: 10, year: 2025);
$client->wages->delete(id: 7); // mzda se maže jen podle id
```

## Přehled agend a CRUD

Stav byl **ověřen proti živému schématu** (`s3api.api.moneys3.eu/graphql/`) — viz
[Validace proti schématu](#validace-proti-schématu). Zápis je vždy asynchronní (importní
fronta), mutace vrací `{ guid, isSuccess }`.

| Agenda | Služba | C | R | U | D |
|--------|--------|---|---|---|---|
| Faktura vystavená | `issuedInvoices` | ✓ | ✓ | ✓ | ✓ |
| Faktura přijatá | `receivedInvoices` | ✓ | ✓ | ✓ | ✓ |
| Bankovní doklad | `bankStatements` | ✓ | ✓ | ✓ | ✓ |
| Pokladní doklad | `cashVouchers` | ✓ | ✓ | ✓ | ✓ |
| Objednávka přijatá | `receivedOrders` | ✓ | ✓ | ✓ | ✓ |
| Objednávka vystavená | `issuedOrders` | ✓ | ✓ | ✓ | ✓ |
| Skladová příjemka | `receivedSlips` | ✓ | ✓ | ✓ | ✓ |
| Skladová výdejka | `issuedSlips` | ✓ | ✓ | ✓ | ✓ |
| Inventurní doklad | `stockTakingDocuments` | ✓ | ✓ | ✓ | ✓ |
| Mzda | `wages` | ✓ | — | ✓ | ✓ |
| Peněžní deník | `cashJournal` | — | ✓ | — | — |
| Účetní deník | `accountingJournal` | — | ✓ | — | — |
| Skladová zásoba | `warehouseStocks` | — | ✓ | — | — |
| Agendy / firmy | `agendas` | — | ✓ | — | — |

> Money S3 GraphQL API vystavuje **`create` / `update` / `delete` + query pro desítky
> dalších agend** (adresář `companies`, kmenové karty `articles`, dodací listy, nabídky,
> poptávky, interní doklady, závazky/pohledávky, číselníky…). Ty zatím nemají vlastní
> typované DTO, ale jdou číst generickým `query(fields: […])` patternem a snadno doplnit —
> viz [Rozšíření o další agendu](#rozšíření-o-další-agendu).

## Validace proti schématu

Knihovna je ověřena proti **živému GraphQL schématu** Money S3 pomocí introspekce
(`__schema` / `__type` nevyžadují OAuth token). Testovací sada `Schema`
(`tests/Schema/LiveSchemaContractTest.php`) kontroluje, že:

- všechny query root fieldy (`issuedInvoices`, `journalTrs`, …) ve schématu existují,
- všechny `create*` / `update*` / `delete*` mutace agend existují,
- enum tokeny, které knihovna posílá (`PriceType`, `AbsenceType`, řazení `SortEnumType`),
  jsou platné hodnoty schématu,
- pole vstupu `IssuedInvoiceInput` odpovídají schématu,
- názvy polí v atributových enumech (`CashJournalAttribute`, `IssuedInvoiceAttribute`, …)
  jsou reálná filtrovatelná pole (`I…FilterInput`).

Pokud je endpoint nedostupný (offline CI), se test sám přeskočí (`markTestSkipped`), takže
nikdy nerozbije pipeline. Spuštění jen této sady:

```bash
docker run --rm -v "$(pwd):/app" -w /app vspoint/php:8.5-fpm-alpine ./vendor/bin/phpunit --testsuite Schema
```

## Jak funguje typovaný GraphQL builder

Jádro je v `src/GraphQL`:

- **`InputObject`** — kontrakt: `toGraphQL(): array` vrací mapu `pole => hodnota`.
  Implementují ho všechna vstupní DTO (`IssuedInvoiceInput`, `Address`, `ShortCutRef`, …).
- **`ValueEncoder`** — serializuje PHP hodnoty do **GraphQL literálů** (ne JSON):
  řetězce v uvozovkách s escapováním, čísla/bool/null bez uvozovek, **enumy jako holé
  tokeny** (`WITHOUT_VAT`), `InputObject`/vnořená pole jako `{ key: value }`, seznamy jako
  `[ … ]`. `null` pole se vynechávají.
- **`Field`** — uzel selekce (`documentNumber` nebo `company { identificationNumber }`).
- **`QueryBuilder` / `MutationBuilder`** — složí celý dokument:
  `query { root(where: …, order: …, skip: …, take: …) { items { …fields } } }`,
  resp. `mutation { createX(x: { … }, definitionXMLTransfer: { … }) { guid isSuccess } }`.
- **`Where` / `Order`** (`src/Filter`) — kompozičně skládané, typované argumenty.

Enumy, které vyžadují jiný token než název case, implementují `GraphQLEnumValue`. Pro
ojedinělý surový fragment existuje `RawGraphQL` (používat střídmě).

## Symfony bundle

Jádro je framework-agnostické; bundle jen registruje a autowiruje připojení.

```php
// config/bundles.php
return [
    // ...
    VsPoint\MoneyS3\Bridge\Symfony\MoneyS3Bundle::class => ['all' => true],
];
```

```yaml
# config/packages/money_s3.yaml
money_s3:
    connection: cloud            # cloud | local
    app_id: '%env(MONEYS3_APP_ID)%'
    cloud:
        domain: '%env(MONEYS3_DOMAIN)%'
    # local:
    #     host: localhost
    #     port: 85
    #     secure: false
    auth:
        grant_type: client_credentials   # client_credentials | password
        client_id: '%env(MONEYS3_CLIENT_ID)%'
        client_secret: '%env(MONEYS3_CLIENT_SECRET)%'
        # username: '%env(MONEYS3_USERNAME)%'
        # password: '%env(MONEYS3_PASSWORD)%'
```

```php
use VsPoint\MoneyS3\Client\MoneyS3Api;

final class InvoiceImporter
{
    public function __construct(private readonly MoneyS3Api $money) {}

    public function run(): void
    {
        $this->money->issuedInvoices->create(/* ... */);
    }
}
```

Autowiruje se `MoneyS3Api` i konkrétní třída (`CloudClient` / `Client`) podle `connection`.

## Rozšíření o další agendu

1. **Atributový enum** v `src/DTO/{Agenda}/{Agenda}Attribute.php` implementující `FieldName`
   (mapuje PHP název → reálné GraphQL filtrovatelné pole, vnořené přes tečku) — pro typované
   `Where` / `Order`.
2. **Vstupní DTO** v `src/DTO/{Agenda}/` implementující `InputObject` (jen `toGraphQL()`
   s `pole => hodnota`; `null` pole se automaticky vynechají).
3. **Response DTO** (u read agend) s `public static fromData(Data $d): self` a
   `public static fields(): array` (selekce `Field[]`).
4. **Služba** v `src/Agenda/` rozšiřující `AbstractAgendaService` — `query()` přes
   `queryCollection()` / `queryRaw()`, `create()` / `update()` / `delete()` přes `mutate()`.
5. Přidat `public readonly` property + inicializaci do `src/Client/MoneyS3Api.php`.

Přesné názvy root fieldů, mutací, vstupních polí a enum tokenů ověřte v interaktivním
schématu: <https://s3api.api.moneys3.eu/graphql/> („Browse Schema").

## Vývoj

Vše běží přes Docker image z CI (`vspoint/php:8.5-fpm-alpine`):

```bash
# Instalace závislostí
docker run --rm -v "$(pwd):/app" -w /app vspoint/php:8.5-fpm-alpine composer install

# Testy
docker run --rm -v "$(pwd):/app" -w /app vspoint/php:8.5-fpm-alpine ./vendor/bin/phpunit --testdox

# Statická analýza (PHPStan level 6)
docker run --rm -v "$(pwd):/app" -w /app vspoint/php:8.5-fpm-alpine ./vendor/bin/phpstan analyse

# Code style (ECS) — kontrola / oprava
docker run --rm -v "$(pwd):/app" -w /app vspoint/php:8.5-fpm-alpine ./vendor/bin/ecs check
docker run --rm -v "$(pwd):/app" -w /app vspoint/php:8.5-fpm-alpine ./vendor/bin/ecs check --fix
```

Testy jsou **unit** — neprovádějí žádné síťové volání (HTTP je mockované přes Guzzle
`MockHandler`, transport přes `RecordingTransport`). Ověřují sestavování GraphQL dokumentů,
serializaci typovaných vstupů, parsování odpovědí, OAuth2 a resolving endpointů.

CI (`.gitlab-ci.yml`) má fáze `analyse` (validate + phpstan + ecs) a `test` (phpunit).

## Licence

MIT
