# Changelog
All notable changes to Neat Object components will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Changed
- The property class can't handle types other than string use the adapters instead.
- Repository::one() method will now filter soft-deleted objects.

### Added
- Repository sql method and SQLQuery support.
- Property adapters to replace the switch statement.
- Build methods and builder classes for Relations and References.
- Policy now throws a ClassNotFoundException when the requested class cannot be found.
- Policy will use the static createFromArray method as custom factory when it exists.

### Fixed
- Adding multiple unsaved items to a many relation.

### Removed
- Property->type() method.

## [0.10.1] - 2020-02-13
### Added
- Repository can use a custom factory for creating an entity instance from array.

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
