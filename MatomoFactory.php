<?php

namespace Omnipulse\Matomo;

use Omnipulse\Config;
use Omnipulse\Exception\InvalidConfigException;
use Omnipulse\ProviderFactory;
use Omnipulse\ProviderInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Matomo, from its options:
 *
 *   url    the Matomo installation: https://stats.example.org, https://example.matomo.cloud
 *   site   the site's id (idSite)
 *   token  a token_auth with view access (Administration, Personal, Security); "anonymous" (the
 *          default) for a site whose reports anyone may view
 */
final class MatomoFactory extends ProviderFactory
{
    public function __construct(private readonly ?HttpClientInterface $http = null)
    {
    }

    protected function populate(Config $c): void
    {
        $c->defaults([
            'omnipulse.factory_name' => 'matomo',
            'omnipulse.factory_title' => 'Matomo',
            'omnipulse.required_options' => ['url', 'site'],
            'token' => 'anonymous',
        ]);
    }

    protected function build(Config $c): ProviderInterface
    {
        return new MatomoProvider(
            $this->http ?? throw new InvalidConfigException('The "matomo" provider needs an HTTP client: give one to MatomoFactory.'),
            rtrim((string) $c->string('url'), '/'),
            (string) $c->string('site'),
            $c->string('token') ?? 'anonymous',
        );
    }
}
