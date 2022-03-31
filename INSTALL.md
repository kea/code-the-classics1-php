# Install PHP Extension

## PECL

To run the games you need to install the PHP extensions sdl, sdl_image and sdl_mixer. Those extensions need the corresponding lib you can install via the package manager of your Linux distro or with brew if you use MacOs:

```bash
brew install sdl2 sdl2_image sdl2_mixer
pecl install sdl-beta sdl_image sdl_mixer
```

## PHPBrew

Install the required PHP version and SDL extensions via [PHPBrew](https://github.com/phpbrew/phpbrew).

```bash
phpbrew install 8.1 +default
phpbrew switch php-8.1.2
phpbrew ext install sdl latest
phpbrew ext install github:kea/php-sdl-image
phpbrew ext install github:kea/php-sdl-mixer
```

## From source

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
