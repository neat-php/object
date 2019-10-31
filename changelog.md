# Changelog
All notable changes to Neat Database components will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.10.0] - 2019-10-31
### Removed
- Manager instance and create methods.

## [0.9.5] - 2019-10-31
### Added
- Manager isset method.

## [0.9.4] - 2019-10-30
### Added
- Manager get, set, setFactory and unset methods.

### Deprecated
- Manager instance and create methods.

## [0.9.3] - 2019-10-01
### Fixed
- #15 Multiple unpersisted objects can't be added to a many relation

## [0.9.2] - 2019-07-22
### Fixed
- #13 Allow properties to be nullable (e.g. string|null)

## [0.9.1] - 2019-06-14
### Changed
- Remote key property and variable names

## [0.9.0] - 2019-06-14
### Added
- Last method to the collectible trait
- Support for DateTimeImmutable type hint in entities
- Remove support for relations
- PHP 7.3 support
### Fixed
- Relations trait: manager method is now static
- Prevent adding double relations
- Remote key insert/update/delete algorithm