# OpenKvk Magento 2 Implementation

This integration makes it possible to fetch OpenKvk data for Dutch companies
into your Magento project. Using an API key from [Overheid.io](https://overheid.io)
you can fetch the company data based on KVK number (Chamber of Commerce) or by
postcode and house number.

**Note:** The basic implementation for this project is based on [Hyva](https://hyva.io)
and will not work with Magento's Luma or Blank theme at this moment. This can
be added in the future.

## Installation

This package can be installed using Composer with the following command.

```bash
composer require elgentos/magento2-openkvk
bin/magento2 setup:upgrade
```

## Usage

To use this package, you need to get an API key from [Overheid.io](https://overheid.io)
and you need to configure the extension in **Stores > Configuration > Customer 
Configuration > OpenKvk**.

Also, to display this in the account pages, make sure you add this line to the
file `app/design/frontend/[Namespace]/[Theme]/Magento_Customer/templates/address/form.phtml`:

```
<?= $block->getChildHtml('openkvk_js'); ?>
```

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)
