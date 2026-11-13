# Changelog

## [Unreleased]

## [1.0.0] - 2026-11-13

The first stable version. It requires `omnisense/contracts` `^1.0`, `omnisense/omnipulse` `^1.0` with no stability flag, so `composer require omnipulse/matomo` installs it in a project with Composer's default minimum-stability; the 1.x series follows semantic versioning. A tag `vX.Y.Z` is released by `.github/workflows/release.yml` once the package's CI passes on it.

### Added
- Matomo's usage policy declared as data: `policy.json` (omnisense/contracts' schema), its terms
  read on 2026-11-10, named in `composer.json` (`extra.omnisense.policy`, and
  `omnisense/contracts` required). The provider implements `DeclaresUsagePolicyInterface` and gives
  the policy to its Capabilities; the factory hands the provider's Backoff to its HTTP calls, so
  that a 429 and its `Retry-After` hold every next call. `Tests/PolicyTest.php`.

### Changed
- `omnireach/matomo` is now `omnipulse/matomo` (2026-11-10: the omnireach family is renamed omnipulse,
  this repository's history kept): the namespace `Omnipulse\Matomo`, requiring `omnisense/omnipulse`;
  `composer.json` replaces `omnireach/matomo`, so that a project never installs both.

### Fixed
- A report broken down by device has one `mobile` row: Matomo's smartphone, phablet and feature
  phone rows are merged, their counts summed (the bounce rate and the visit duration read from them
  weighted by visits), where each gave a row labelled `mobile`.
- A `device` filter takes the family's units: `mobile` is sent as Matomo's three mobile types ORed
  (`deviceType==smartphone,deviceType==phablet,deviceType==feature%20phone`), where Matomo refused
  `deviceType==mobile`; other values are sent lower-cased.
