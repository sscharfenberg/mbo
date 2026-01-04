# MTG Binder Organizer

Work-In-Progress. Not usable right now.

## Requirements

## Installation

## Setup IDE for development

I am using IntelliJ, other IDEs probably work as well; I just don't know them.

### A) Prettier

Prettier needs to be run on save.

#### IntelliJ

* `Settings` → `Languages & Frameworks` → `Javascript` → `Prettier`
* Select `Automatic Prettier configuration`
* Run for files: `**/*.{js,ts,json,vue,scss}`
* `Run on save` must be checked

### B) ESLint

ESLint should be run while editing in the IDE.

#### IntelliJ

* `Settings` → `Languages & Frameworks` → `Javascript` → `Code Quality Tools` → `ESLint`
* Select `Automatic ESLint configuration`
* Run for files: `**/*.{js,ts,html,vue}`
* `Run on eslint --fix on save` must be checked.

### C) Stylelint

StyleLint should be run while editing in the IDE. This does not work well in `.vue` files currently.

#### IntelliJ

* `Settings` → `Languages & Frameworks` → `Style Sheets` → `Stylelint`
* Select `Enable`
* Run for files: `**/*.{scss, vue}`
* `Run on stylelint --fix on save` must be checked

## Artisan commands

### `php artisan scryfall:sets [--full]`

This command gets all the sets from scryfall and updates the database accordingly. `--full` option cleans `set-icon` storage disk before contacting scryfall, without the option only missing `.svg` files will get downloaded. Use the normal command daily, and the `--full` update once every week.

## NPM commands

### `npm run dev`

Development mode. To ensure dev mode works:
* Modify `.env`:
```.env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://mixtape.local
```
* `APP_URL` needs to point to the correct local server. Use `hosts` file to forward the hostname to the server ip.
* `public` directory on the server needs to have the `hot` file.
* Ensure `public/.htaccess` file does not have `AuthType Basic` entries - this will screw up cors / deliverability of js bundle.

### `npm run build`

Production mode. To ensure production mode works:
* Modify `.env`:
```.env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://[yourhostname].noip.com
```
* `APP_URL` needs to point to the correct server domain. Use a ddns service like `noip` etc.
* The `public` directory on the server *must not* have a `hot` file.
* Upload all files in `public` directory to the server.

### `npm run lint`

Run Eslint and Stylelint separately.

## License
`MixTape` is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
