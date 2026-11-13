# omnipulse/matomo

Formerly `omnireach/matomo` (renamed on 2026-11-10 with its family: `Omnireach\` is now `Omnipulse\`;
this package replaces the former name).

[omnisense/omnipulse](https://github.com/glitchr-omnisense/omnipulse)'s Matomo, under any PHP host:
visits, visitors, pageviews, bounce rate and visit length of a site, by entry page, page,
referrer, country, device or day, through Matomo's Reporting API - a self-hosted Matomo or Matomo
Cloud.

## Install and a first call

```sh
composer require omnipulse/matomo
```

No account for this first call: Matomo's answer is recorded (`Omnipulse\Testing\RecordedHttpClient`
answers in place of the API), so nothing leaves the process. A real call needs a Matomo instance
(one's own or Matomo Cloud), the site's id and a token with view access (anonymous only where the
instance allows it).

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Omnipulse\Matomo\MatomoFactory;
use Omnipulse\Model\{Metric, ReachQuery};
use Omnipulse\Testing\RecordedHttpClient;

// Matomo's answer, recorded: nothing sent. HttpClient::create() (symfony/http-client) for a real call.
$http = RecordedHttpClient::routes([
    'POST /index.php' => '{"nb_visits": 100103, "nb_pageviews": 195851, "bounce_rate": "67%", "avg_time_on_site": 181}',
]);
$matomo = (new MatomoFactory($http))->create(['url' => 'https://stats.example.org', 'site' => '1', 'token' => 'recorded']);

$report = $matomo->audience(new ReachQuery(new DateTimeImmutable('2026-10-01'), new DateTimeImmutable('2026-10-07'), [], [Metric::Visits, Metric::Pageviews]));
echo json_encode($report->rows[0]->metrics, JSON_UNESCAPED_SLASHES), "\n";   // {"visits":100103,"pageviews":195851}
```

With your credentials, through the family's registry:

```php
use Omnipulse\Matomo\MatomoFactory;
use Omnipulse\Registry;
use Symfony\Component\HttpClient\HttpClient;

$registry = new Registry([new MatomoFactory(HttpClient::create())], [
    'stats' => ['factory' => 'matomo', 'options' => ['url' => 'https://stats.example.org', 'site' => '1', 'token' => '...']],
]);
```

Options: `url`, `site` (idSite), `token` (a token_auth with view access; `anonymous`, the
default, for reports anyone may view).

## The API

The Reporting API (<https://developer.matomo.org/api-reference/reporting-api>), always by POST
with the token in the body (never in a URL, where logs keep it): `API.get` for the whole site,
`Actions.getEntryPageUrls`, `Actions.getPageUrls`, `Referrers.getAll`, `UserCountry.getCountry`,
`DevicesDetection.getType` for a breakdown (`flat=1`), `period=range` over the query's days or
`period=day` for a history. One dimension at a time besides the date (`Capabilities::$combined`
false). Filters on the source, the country and the device become a Matomo segment; a filter on a
page or an entry page works in a report broken down by it.

Units: entry-page metrics are those of the visits that started there (`entry_nb_visits`,
`entry_nb_actions`, `entry_bounce_count`); a bounce rate is bounces over visits; a visit length is
the visit seconds over visits. Distinct visitors over a range are Matomo's own only when the
installation processes them, otherwise the sum of each day's (the report's note says so).

**Terms**: a self-hosted installation's limits are its own (archiving by browser or cron); Matomo
Cloud's are its plan's. Reuse: no PHP client of the Reporting API was found to reuse (Matomo's
own PHP package, matomo/matomo-php-tracker, sends tracking hits, which is not this).

## Usage policy

Matomo's usage policy is declared as data in [`policy.json`](policy.json) (omnisense/contracts'
schema, named in `composer.json`'s `extra.omnisense.policy`), its terms read on
2026-11-10 (https://developer.matomo.org/api-reference/reporting-api): no rate limit declared: an installation's limits are its own (`rateLimits: null`); rights unknown (the site owner's data); no key required (`anonymous`). omnipulse's registry enforces it - a call over a
limit is never made, a 429 and its `Retry-After` hold every next call
(`UsageLimitReachedException`) - and a site may tighten it, never loosen it
(omnisense/omnipulse's docs/policies.md).

## Verified, and not

- Verified (the core's harness, PHP 8.4): the whole site, entry pages, a daily history by country
  with a device segment, the token by POST, what Matomo cannot answer, a refused token - against
  answers recorded from Matomo's public demo on 2026-10-24 (trimmed of unread fields); every class
  loading with the package's requirements alone (`BareTest`).
- Verified live (`docker compose run --rm omnipulse live`, 2026-10-24): demo.matomo.cloud, site 1,
  anonymous - the whole site over the last seven days, then entry pages over two days.
- Not verified: a self-hosted installation with a token.

## Licence

MIT. An unofficial client, affiliated with Matomo (InnoCraft) in no way.
