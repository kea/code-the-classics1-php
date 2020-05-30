:warning: The assets are Creative Commons Attribution-NonCommercial- ShareAlike 3.0 Unported (CC BY-NC-SA 3.0) by [Code the classics Vol. 1](https://wireframe.raspberrypi.org/books/code-the-classics1) :warning: 

# Boing!

_Boing_ is a porting in PHP of the first game of [Code the classics Vol. 1](https://wireframe.raspberrypi.org/books/code-the-classics1) originally made in Python.

It's not a web game with the backend in PHP but a fully PHP game. 

It is possible thanks to the [Php-SDL extension](https://github.com/Ponup/php-sdl).
I've [patched](https://github.com/kea/php-sdl/tree/sdl-image-load) the current version to enable SDL_Image, and I've to decide if make a pull request to the original repo (Php-SDL) or create a simple extension to handle only that SDL_Image extension in pair with Php-SDL. :thinking_face:

## Installation

To install the extension you need sdl2 and sdl2_image. You can install the package from your Linux distro or for mac users with brew:

```
brew install sdl2 sdl2_image
```

Install the patched Php-SDL extensions from source:
```bash
git clone https://github.com/kea/php-sdl.git
cd php-sdl
git checkout sdl-image-load
phpize
./configure
make
make install
```

Enable the extension on your `php.ini` file or where your enable php extensions.

Then you can clone this repo e run composer to install dependencies:

```bash
git clone https://github.com/kea/code-the-classics-php.git
cd php-sdl
composer install
```

## Run the game

```
php src/Boing/boing.php
```

Menu selection <kbd>Up</kbd>, <kbd>Down</kbd> and <kbd>Space</kbd> to confirm.

Player 1 keys: <kbd>Up</kbd> or <kbd>a</kbd> and <kbd>Down</kbd> or <kbd>z</kbd>

Player 2 keys: <kbd>k</kbd> and <kbd>m</kbd>
