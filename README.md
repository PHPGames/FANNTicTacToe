ANN Tic Tac Toe in a Symfony Console App
========================================

Project for *ANN Tic Tac Toe in a Symfony Console App* SymfonyCon Madrid 2014

## Requirements

* PHP >= 5.5
* PHP FANN extension (install: https://github.com/bukka/php-fann#installation)

## How to install

Clone the repository an run: 

```shell
composer install
```

## Usage

```shell
php bin/console
```

## Logs and files

The application will create some files in `/tmp` folder.

### Log files

- `human_vs_machine.log`
- `fann_player.log`

### Training files

- `training_*.dat`

### Artificial Neural Network file

- `ann.net`
