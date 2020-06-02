:warning: The assets are Creative Commons Attribution-NonCommercial- ShareAlike 3.0 Unported (CC BY-NC-SA 3.0) by [Code the classics Vol. 1](https://wireframe.raspberrypi.org/books/code-the-classics1) :warning: 

# Boing!

_Boing_ is a porting in PHP of the first game of [Code the classics Vol. 1](https://wireframe.raspberrypi.org/books/code-the-classics1) originally made in Python.

It's not a web game with the backend in PHP but a fully PHP game. 

It is possible thanks to the [Php-SDL extension](https://github.com/Ponup/php-sdl).
You need also two PHP extensions for [SDL_mixer](https://github.com/kea/php-sdl-mixer) (sound) and [SDL_image](https://github.com/kea/php-sdl-image) (multiple image format loader).

## Installation

To install the extension you need sdl2, sdl2_image and sdl_mixer. You can install the package from your Linux distro or for mac users with brew:

```
brew install sdl2 sdl2_image sdl2_mixer
```

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

Install the Php-SDL-image extensions from source:
```bash
git clone https://github.com/kea/php-sdl-image.git
cd php-sdl-image
phpize
./configure
make
make install
cd ..
```

Install the Php-SDL-mixer extensions from source:
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

Then you can clone this repo e run composer to dump autoload:

```bash
git clone https://github.com/kea/code-the-classics1-php.git
cd code-the-classics1-php
composer install
```

## Run the game

```
php src/Boing/boing.php
```

Menu selection <kbd>Up</kbd>, <kbd>Down</kbd> and <kbd>Space</kbd> to confirm.

Player 1 keys: <kbd>Up</kbd> or <kbd>a</kbd> and <kbd>Down</kbd> or <kbd>z</kbd>

Player 2 keys: <kbd>k</kbd> and <kbd>m</kbd>
