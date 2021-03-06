<?hh // strict
/*
 *  Copyright (c) 2017-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace Facebook\HHAST\__Private\StrP;

use namespace HH\Lib\Str;

function upper_camel(string $in): string {
  return \preg_replace_callback(
    '/(^|_)([a-z])/',
    $matches ==> Str\uppercase($matches[2]),
    $in,
  );
}

<<__Memoize>>
function underscored(string $in): string {
  return \preg_replace_callback(
    '/[A-Z][a-z]+/',
    $matches ==> '_'.Str\lowercase($matches[0]),
    $in,
  )
    |> Str\strip_prefix($$, '_');
}
