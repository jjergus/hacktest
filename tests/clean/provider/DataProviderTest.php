<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

use function Facebook\FBExpect\expect;
use type Facebook\HackTest\{DataProvider, HackTest};
use namespace HH\Lib\Str;

// @oss-disable: <<Oncalls('hack')>>
final class DataProviderTest extends HackTest {

  public function provideSimple(): vec<(Traversable<arraykey>)> {
    $elements = vec['the', 'quick', 'brown', 'fox', 1];
    return vec[
      tuple($elements),
      tuple(new Vector($elements)),
      tuple(new Set($elements)),
      tuple(new Map($elements)),
      tuple(vec($elements)),
      tuple(keyset($elements)),
      tuple(dict($elements)),
      tuple(HackLibTestTraversables::getIterator($elements)),
    ];
  }

  <<DataProvider('provideSimple')>>
  public function testSimple(Traversable<arraykey> $traversable): void {
    expect(Str\join($traversable, '-'))->toBeSame('the-quick-brown-fox-1');
  }

  public function provideMultipleArgs(): vec<(int, int)> {
    return vec[
      tuple(1, 2),
      tuple(2, 1),
    ];
  }

  <<DataProvider('provideMultipleArgs')>>
  public function testMultipleArgs(int $a, int $b): void {
    expect($a + $b)->toBeSame(3);
  }

  public function provideSkip(): vec<(int)> {
    self::markTestSkipped(
      'This test depends on a data provider that is not ready yet.',
    );
  }

  <<DataProvider('provideSkip')>>
  public function testProviderSkip(int $_a): void {}

  <<DataProvider('provideSkip')>>
  public function testProviderSkipDup(int $_a): void {}

  public static function provideStatic(): vec<(string, string)> {
    return vec[tuple('foo', 'bar'), tuple('herp', 'derp')];
  }

  <<DataProvider('provideStatic')>>
  public function testStaticProvider(string $a, string $b): void {
    $expected = dict['foo' => 'bar', 'herp' => 'derp'];
    expect($b)->toBeSame($expected[$a] ?? null);
  }
}
