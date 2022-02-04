:warning: The assets are Creative Commons Attribution-NonCommercial- ShareAlike 3.0 Unported (CC BY-NC-SA 3.0) by [Code the classics Vol. 1](https://wireframe.raspberrypi.org/books/code-the-classics1) :warning: 

# Code the classics

This project is a porting in PHP of the games of [Code the classics Vol. 1](https://wireframe.raspberrypi.org/books/code-the-classics1) originally developed in Python.

It's not a web game with the backend in PHP but a fully PHP game. 

It is possible thanks to the [Php-SDL extension](https://github.com/Ponup/php-sdl).
You need also two PHP extensions for [SDL_mixer](https://github.com/kea/php-sdl-mixer) (sound) and [SDL_image](https://github.com/kea/php-sdl-image) (multiple image format loader).

# Porting status

- Boing! [Ready]
- Cavern [Ready]
- Infinite bunner [WIP]
- Myriapod [TBD]
- Substitute Soccer [TBD]

## Installation

To install the extension, you need sdl2, sdl2_image and sdl_mixer. You can install the package from your Linux distro or for mac users with brew:

```bash
brew install sdl2 sdl2_image sdl2_mixer
```

### PHPBrew

Install the required PHP version and SDL extensions via [PHPBrew](https://github.com/phpbrew/phpbrew).

```bash
phpbrew install 8.1 +default
phpbrew switch php-8.1.2
phpbrew ext install sdl latest
phpbrew ext install github:kea/php-sdl-image
phpbrew ext install github:kea/php-sdl-mixer
```

### From source

To install all SDL extensions, you require php 8.0+.

Install Php-SDL extension from source:
```bash
git clone https://github.com/Ponup/php-sdl.git
cd php-sdl
phpize
./configure
make
make install
cd ..
```

Install Php-SDL-image extensions from source:
```bash
git clone https://github.com/kea/php-sdl-image.git
cd php-sdl-image
phpize
./configure
make
make install
cd ..
```

Install Php-SDL-mixer extensions from source:
```bash
git clone https://github.com/kea/php-sdl-mixer.git
cd php-sdl-mixer
phpize
./configure
make
make install
cd ..
```

Enable the three extensions on your `php.ini` file or where your enable php extensions.

## Run the games

You have to clone this repo (or download the archive) and run composer:

```bash
git clone https://github.com/kea/code-the-classics1-php.git
cd code-the-classics1-php
composer install
```

### Boing
```bash
php src/Boing/boing.php
```

Menu selection <kbd>Up</kbd>, <kbd>Down</kbd> and <kbd>Space</kbd> to confirm.

Player 1 keys: <kbd>Up</kbd> or <kbd>a</kbd> and <kbd>Down</kbd> or <kbd>z</kbd>

Player 2 keys: <kbd>k</kbd> and <kbd>m</kbd>

### Cavern

```bash
php src/Cavern/cavern.php
```

### Bunner

```bash
php src/Bunner/bunner.php
```
