# Changelog

Всички съществени промени по модула CreditJet (PB Personal Finance) се документират в този файл.

Форматът следва [Keep a Changelog](https://keepachangelog.com/en/2.0.0/),
а версиите следват [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.9.1] - 2026-07-26

### Added

- Метод `Creditjet::sanitizeBankField()` за премахване на `;` от стойностите преди изпращане към банката.
- CycloneDX Composer plugin (`cyclonedx/cyclonedx-php-composer`) като dev зависимост за генериране на SBOM.

### Fixed

- Санитизация на текстовите полета в bank payload-а (име, фамилия, ЕГН, телефон, имейл, категория, марка, варианти), за да не се чупи автоматичната обработка при наличие на `;` в данните:
  - продуктова страница — `JetSend.php`
  - кошница — `JetSendCart.php`
  - чекаут — `validation.php`
- Липсваща крайна точка и запетая (`;`) на реда за месечна вноска в чекаута (`validation.php`).
- Възможно недефинирани card променливи в `JetCalculate.php` (`$jet_vnoska_card` и свързаните card стойности) при статичен анализ / условни блокове.
- Възможно недефинирана променлива `$f` във функцията `RATE()` в `JetCalculate.php` при лихва близка до нула.

### Changed

- Версия на модула: `1.8.3` → `1.9.1` (`creditjet.php`, `config.xml`, `config_bg.xml`).

## [1.8.3] - 2026-04-17

Предишен release. Подробностите за промените са в git историята преди тази версия.

[Unreleased]: https://github.com/wiley68/jetcredit-ps9/compare/v1.9.1...HEAD
[1.9.1]: https://github.com/wiley68/jetcredit-ps9/compare/v1.8.3...v1.9.1
[1.8.3]: https://github.com/wiley68/jetcredit-ps9/releases/tag/v1.8.3
