<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Bridge\Symfony;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use VsPoint\MoneyS3\Auth\Credentials;
use VsPoint\MoneyS3\Client\Client;
use VsPoint\MoneyS3\Client\CloudClient;
use VsPoint\MoneyS3\Client\MoneyS3Api;

/**
 * Thin Symfony integration. Wires a {@see MoneyS3Api} connection ({@see CloudClient} or
 * {@see Client}) from configuration and makes it autowireable.
 *
 * Example configuration (config/packages/money_s3.yaml):
 *
 *     money_s3:
 *         connection: cloud
 *         app_id: '%env(MONEYS3_APP_ID)%'
 *         cloud:
 *             domain: '%env(MONEYS3_DOMAIN)%'
 *         auth:
 *             grant_type: client_credentials
 *             client_id: '%env(MONEYS3_CLIENT_ID)%'
 *             client_secret: '%env(MONEYS3_CLIENT_SECRET)%'
 */
final class MoneyS3Bundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
            ->enumNode('connection')
            ->values(['cloud', 'local'])->defaultValue('cloud')->end()
            ->scalarNode('app_id')
            ->isRequired()
            ->cannotBeEmpty()
            ->end()
            ->arrayNode('cloud')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('domain')
            ->defaultNull()
            ->end()
            ->end()
            ->end()
            ->arrayNode('local')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('host')
            ->defaultValue('localhost')
            ->end()
            ->integerNode('port')
            ->defaultValue(85)
            ->end()
            ->booleanNode('secure')
            ->defaultFalse()
            ->end()
            ->end()
            ->end()
            ->arrayNode('auth')
            ->isRequired()
            ->children()
            ->enumNode('grant_type')
            ->values(['client_credentials', 'password'])->defaultValue('client_credentials')->end()
            ->scalarNode('client_id')
            ->isRequired()
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('client_secret')
            ->isRequired()
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('username')
            ->defaultNull()
            ->end()
            ->scalarNode('password')
            ->defaultNull()
            ->end()
            ->end()
            ->end()
            ->end();
    }

    /**
     * @param array<string, mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        /** @var array{grant_type: string, client_id: string, client_secret: string, username: ?string, password: ?string} $auth */
        $auth = $config['auth'];

        $credentials = new Definition(Credentials::class);
        if ($auth['grant_type'] === 'password') {
            $credentials->setFactory([Credentials::class, 'password']);
            $credentials->setArguments(
                [$auth['client_id'], $auth['client_secret'], $auth['username'], $auth['password']]
            );
        } else {
            $credentials->setFactory([Credentials::class, 'clientCredentials']);
            $credentials->setArguments([$auth['client_id'], $auth['client_secret']]);
        }
        $builder->setDefinition('money_s3.credentials', $credentials);

        /** @var string $appId */
        $appId = $config['app_id'];

        if (($config['connection'] ?? 'cloud') === 'local') {
            /** @var array{host: string, port: int, secure: bool} $local */
            $local = $config['local'];
            $client = new Definition(Client::class, [
                $appId,
                new Reference('money_s3.credentials'),
                $local['host'],
                $local['port'],
                $local['secure'],
            ]);
            $concreteClass = Client::class;
        } else {
            /** @var array{domain: ?string} $cloud */
            $cloud = $config['cloud'];
            $client = new Definition(CloudClient::class, [
                $cloud['domain'],
                $appId,
                new Reference('money_s3.credentials'),
            ]);
            $concreteClass = CloudClient::class;
        }

        $client->setPublic(true);
        $builder->setDefinition(MoneyS3Api::class, $client);
        $builder->setAlias($concreteClass, MoneyS3Api::class)->setPublic(true);
    }
}
