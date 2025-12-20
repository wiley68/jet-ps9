# CreditJet Payment Module for PrestaShop 9.x

Credit calculator and payment through Piraeus Personal Finance for PrestaShop 9.x

## Installation

1. Copy the module to the `modules/creditjet/` directory
2. Install the module through PrestaShop administration
3. Configure the module settings

## Settings

- **Merchant ID**: Your CreditJet Merchant ID
- **API Key**: Your CreditJet API Key
- **Sandbox Mode**: For testing the integration
- **Minimum Amount**: Minimum amount for credit payment
- **Maximum Amount**: Maximum amount for credit payment

## Module Structure

```
modules/creditjet/
├── composer.json           # Composer configuration
├── config.xml             # Module configuration
├── creditjet.php          # Main module class
├── index.php              # Direct access protection
├── .intelephense-stubs.php # Stubs to avoid editor errors
├── controllers/
│   └── front/
│       └── validation.php # Payment validation controller
├── src/                   # PSR-4 classes
├── views/
│   ├── css/
│   │   └── front.css      # CSS styles
│   ├── js/
│   │   └── front.js       # JavaScript functionality
│   └── templates/
│       └── hook/
│           └── payment_return.tpl # Post-payment return template
└── translations/          # Translation files
```

## Missing Files

For full module functionality, you need to add:

- `logo.png` - Module logo (250x60px recommended)
- Translation files in `translations/bg.php` and `translations/en.php`
- Additional classes in `src/` directory for CreditJet API integration

## Development

The module uses:
- PHP 8.1+
- PrestaShop 9.0+
- PSR-4 autoloading
- Composer for dependency management

## Hooks

The module registers the following hooks:
- `paymentOptions` - For displaying payment option
- `paymentReturn` - For handling post-payment return
- `displayHeader` - For adding CSS/JS
- `displayBackOfficeHeader` - For back office resources

## Compatibility

- PrestaShop 9.0+
- PHP 8.1+
- MySQL/MariaDB
- Currencies: BGN, EUR
