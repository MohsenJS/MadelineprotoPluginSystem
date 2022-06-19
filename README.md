<div id="top"></div>

[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![GPL3 License][license-shield]][license-url]
[![Telegram][telegram-shield]][telegram-url]

<br />
<div align="center">
  <a href="https://github.com/OxMohsen/MadelineprotoPluginSystem">
      <img src="images/logo.png" alt="Madelineproto Plugin System Logo" width="80" height="80">
  </a>

<h3 align="center">MadelineprotoPluginSystem</h3>

  <p align="center">
    plugin system for madelineproto
    <br />
    <a href="https://github.com/OxMohsen/MadelineprotoPluginSystem/issues">Report Bug</a>
    ·
    <a href="https://github.com/OxMohsen/MadelineprotoPluginSystem/issues">Request Feature</a>
  </p>
</div>

<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
  </ol>
</details>

## About The Project
[![Product Name Screen Shot][product-screenshot]](https://github.com/OxMohsen/MadelineprotoPluginSystem)

This is a OOP plugins system for [MadelineProto](https://docs.madelineproto.xyz/).
This project aims to provide a platform where you can simply write a plugin for madelineproto and simply use it or share it with
other people.

The project can:
- Supports all types and methods according to MadelineProto 7.0.
- New web UI.
- Async plugins.
- Regex pattern for plugins.
- Conversation feature.
- [new update helper](#new-update-helper)

<p align="right">(<a href="#top">back to top</a>)</p>

### Built With

* [PHP](https://www.php.net/)
* [MadelineProto](https://docs.madelineproto.xyz/)
* [symfony/console](https://symfony.com/doc/current/components/console.html)

<p align="right">(<a href="#top">back to top</a>)</p>

## Getting Started

for using this plugins system you need to follow the following steps.

### Prerequisites
You'll need Git and composer.
* [Git](https://git-scm.com/downloads)
* [Composer](https://getcomposer.org/download/)

### Installation

1. Clone the repo
   ```sh
   git clone https://github.com/OxMohsen/MadelineprotoPluginSystem.git
   ```
2. Go to project directory:
   ```sh
   cd MadelineprotoPluginSystem
   ```
3. Install packages with composer
   ```sh
   composer install
   ```
4. Edit `Config.php` and then replace all necessary values with those of your project.

<p align="right">(<a href="#top">back to top</a>)</p>

## Usage

### use through terminal.
* you can run the project with default plugins by using following terminal command.
   ```sh
   php index.php
   ```
* if you want to add new plugin, run this command:
   ```sh
   php helper plugin:make
   ```
* if you want to remove a plugin, run this command:
   ```sh
   php helper plugin:remove
   ```
* if you want to see plugin list, run this command:
   ```sh
   php helper plugin:list
   ```

### use through browser.
After installation and configuration, create a zip of the project contents and transfer it to your host.
extract the zip and open the folder url in your browser.

### new update helper
in this project, you can simply access MadelineProto update in plugin via update helper.
it's just need to use
   ```php
   $this->MadelineProto->update->getUpdate()->get('DOT.INDEX.SEPARATOR');
   ```
in your plugin class. for example if you want to access `$update['message']['media']`, you can use
   ```php
   $this->MadelineProto->update->getUpdate()->get('message.media');
   ```
if helper can find it, media will be returned otherwise `null` will be returned.
<p align="right">(<a href="#top">back to top</a>)</p>


## Plugin list

| Plugin Usage | Description | Admin Only? |
| :--- | :--- | :---: |
| !broadcast | send a message to all bot chats. | **YES** |
| !phpdoc "function" | send php documentation of target function. | **YES** |
| !delmsgs 100 | delete some messages from supergroups or channel. | **YES** |
| !hash "string" | send the md5, sha1, sha256, sha512 hash of the string. | **YES** |
| !logout | terminate the robot session. | **YES** |
| !restart | forcefully restart and apply changes. (Only work if running via web) | **YES** |
| !shutdown | shut the bot down. | **YES** |
| !generatepassword | send strong password. | **NO** |
| !help | send plugins usage. | **NO** |

<p align="right">(<a href="#top">back to top</a>)</p>

## Roadmap

- [ ] add new plugins
    - [ ] eval plugin

See the [open issues](https://github.com/OxMohsen/MadelineprotoPluginSystem/issues) for a full list of proposed features (and known issues).

<p align="right">(<a href="#top">back to top</a>)</p>

## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<p align="right">(<a href="#top">back to top</a>)</p>

## License

Distributed under the GPL3 License. See `LICENSE` for more information.

<p align="right">(<a href="#top">back to top</a>)</p>

## Contact

Mohsen Falakedin - [@OxMohsen](https://t.me/OxMohsen) - oxmohsen@oxmohsen.ir

Project Link: [https://github.com/OxMohsen/MadelineprotoPluginSystem](https://github.com/OxMohsen/MadelineprotoPluginSystem)

<p align="right">(<a href="#top">back to top</a>)</p>

[contributors-shield]: https://img.shields.io/github/contributors/OxMohsen/MadelineprotoPluginSystem.svg?style=for-the-badge
[contributors-url]: https://github.com/OxMohsen/MadelineprotoPluginSystem/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/OxMohsen/MadelineprotoPluginSystem.svg?style=for-the-badge
[forks-url]: https://github.com/OxMohsen/MadelineprotoPluginSystem/network/members
[stars-shield]: https://img.shields.io/github/stars/OxMohsen/MadelineprotoPluginSystem.svg?style=for-the-badge
[stars-url]: https://github.com/OxMohsen/MadelineprotoPluginSystem/stargazers
[issues-shield]: https://img.shields.io/github/issues/OxMohsen/MadelineprotoPluginSystem.svg?style=for-the-badge
[issues-url]: https://github.com/OxMohsen/MadelineprotoPluginSystem/issues
[license-shield]: https://img.shields.io/github/license/OxMohsen/MadelineprotoPluginSystem.svg?style=for-the-badge
[license-url]: https://github.com/OxMohsen/MadelineprotoPluginSystem/blob/master/LICENSE
[telegram-shield]: https://img.shields.io/badge/-telegram-black.svg?style=for-the-badge&logo=telegram&colorB=555
[telegram-url]: https://t.me/oxmohsen
[product-screenshot]: images/screenshot.png
