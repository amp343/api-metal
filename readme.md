## amp343/api-metal

<img width="70" alt="screen shot 2016-04-05 at 7 08 22 pm" src="https://cloud.githubusercontent.com/assets/425365/14300951/d07f2406-fb61-11e5-9db5-670cbbb1c2b1.png">

```
Micro micro foundation for creating a happy little php7 api in minutes
```

## Overview

- Based on simple API concerns, with helpful things added.
- Updated to leverage PHP7 goodness.

#### What is metal?

- metal is a material that provides support without a bunch of fanfare.
- referring to the phrase `bare metal`, meaning, `just the essentials`.

#### Benefits

- rely on metal to provide:
  - common, easily extended classes for:
    - `Request`
    - `Response`
    - `Route`
    - `MetalController`
    - `MetalRouter`
    - `Parameter`
    - `Error`
  - a simple dsl for defining, handling, and fulfilling routes
  - a simple dsl for request parameter validation
  - helpful convenience methods for controllers (`getParam()`, `getHeader()`, `validate()`, etc)
  - error definitions & easy handling for http errors
  - de facto response handling for json or xml

#### Quick Example: working API

See: https://github.com/amp343/api-metal/tree/master/example

## Getting started

#### Get a working API in 4 steps:

1. Install `amp343/api-metal`
2. Define some `Route`s using simple syntax
3. Define a `Controller` to handle requests
4. Invoke the built-in request handler

...

5. See your API working.

## Tests

#### Coverage

<img width="566" alt="screen shot 2016-04-05 at 9 47 57 pm" src="https://cloud.githubusercontent.com/assets/425365/14303381/20f2eefc-fb78-11e5-991e-f819fca79b35.png">

<img width="1153" alt="screen shot 2016-04-05 at 9 47 12 pm" src="https://cloud.githubusercontent.com/assets/425365/14303382/20f5c6ea-fb78-11e5-803f-88fe0420a91d.png">


`./vendor/bin/phpunit`

**With coverage:**

`phpdbg -qrr ./bin/phpunit tests --coverage-html reports`

(you will need to have phpdbg installed in order to do this, for instance, if locally with Homebrew, `brew install php70 --with-phpdbg`)
