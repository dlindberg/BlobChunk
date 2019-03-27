# blob-chunk

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This is currently an early work in progress. The purpose of this project is to take a content block of html and break it apart into smaller chunks to make to improve indexing with search appliances such as Algolia, where frequently the raw html content is too large to fit within the index limits. 

## Install

Via Composer

``` bash
$ composer require dlindberg/blob-chunk
```

## Basic Usage

``` php
$blobChunk = new dlindberg\BlobChunk();
$result = $blobChunk->parse($html);
```

Returns an array of content chunks. By default it attempts to break out lists, tables, header tags, and paragraphs as separate elements. It also breaks apart paragraphs into sentences. There is a reasonable amount of surface area for extensibility and configuration; however, that area of the project is still somewhat of a work in progress.  

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

The current tests for the manager are reasonably thorough. Tests on the parser and parent class need to be improved.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email dane@lindberg.xyz instead of using the issue tracker.

## Credits

- [Dane Lindberg][link-author]
- [All Contributors][link-contributors]

The boiler plate for this project is based on [ The League of Extraordinary Packages'](http://thephpleague.com) [Skeleton](https://github.com/thephpleague/skeleton) package repository.
## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/dlindberg/blob-chunk.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/dlindberg/blob-chunk/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/dlindberg/blob-chunk.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/dlindberg/blob-chunk.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/dlindberg/blob-chunk.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/dlindberg/blob-chunk
[link-travis]: https://travis-ci.org/dlindberg/blob-chunk
[link-scrutinizer]: https://scrutinizer-ci.com/g/dlindberg/blob-chunk/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/dlindberg/blob-chunk
[link-downloads]: https://packagist.org/packages/dlindberg/blob-chunk
[link-author]: https://github.com/dlindberg
[link-contributors]: ../../contributors
