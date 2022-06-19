
# MadelineprotoPluginSystem

a plugin system for [MadelineProto](https://github.com/danog/MadelineProto)

## Installation

clone this repository using git:

```bash
$ git clone https://github.com/OxMohsen/MadelineprotoPluginSystem.git
```

go to the `MadelineprotoPluginSystem` folder:
```bash
$ cd MadelineprotoPluginSystem
```

Now you can install all dependencies using [Composer](https://getcomposer.org/)

```bash
$ composer update
```
### Configuration

edit the `src/Config.php`

## Usage

#### Run bot

```bash
$ php index.php
```

#### Add plugin

```bash
$ php helper plugin:make
```

#### Remove plugin

```bash
$ php helper plugin:remove
```

#### List all plugins

```bash
$ php helper plugin:list
```
