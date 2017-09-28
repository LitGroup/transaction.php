# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## 0.2.1 - 2017-09-29
### Fixed
- `TransactionManager::runTransactional()` does not call `TransactionHandler::rollBack()`
  after exception thrown by `TransactionHandler::commit()`.

## 0.2.0 - 2017-09-18
### Changed
- `LitGroup\Transaction\Exception\StateException` was moved
  to `LitGroup\Transaction\StateException`.

### Removed
- `Exception\TransactionException`.

## 0.1.0 - 2017-09-11
- Initial version.