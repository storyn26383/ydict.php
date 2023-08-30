# ydict.php

Command line Yahoo Dictionary for geeks, inspired by [sayuan/ydict.js](https://github.com/sayuan/ydict.js/).

[![Build Status](https://github.com/storyn26383/ydict.php/actions/workflows/tests.yml/badge.svg?branch=master)](https://github.com/storyn26383/ydict.php/actions/workflows/tests.yml)

![ydict.php](https://i.imgur.com/kzC0qcc.png)

## Installation

### via Composer

```bash
composer global require sasaya/ydict.php
```

### Manual Download of PHAR

```bash
wget https://github.com/storyn26383/ydict.php/raw/master/build/ydict.php.phar
chmod +x ydict.php.phar
sudo mv ydict.php.phar /usr/local/bin/ydict.php
```

## Usage

```bash
ydict.php <word>
```

### For detailed explanation

```bash
ydict.php -v <word>
```
