<?hh // strict
/*
 *  Copyright (c) 2017-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace Facebook\HHAST\__Private\LSPImpl;

use namespace Facebook\TypeAssert;
use namespace Facebook\HHAST\__Private\{LSP, LSPLib};
use namespace HH\Lib\Str;
use type Facebook\HHAST\__Private\LintRunConfig;

final class InitializeCommand extends LSPLib\InitializeCommand<ServerState> {

  const type TInitializationOptions = shape(
    ?'lintMode' => LintMode,
  );

  const LSP\ServerCapabilities
    SERVER_CAPABILITIES = shape(
      'textDocumentSync' => shape(
        'save' => shape(
          'includeText' => false,
        ),
        'openClose' => true,
      ),
      'codeActionProvider' => true,
      'executeCommandProvider' => shape(
        'commands' => ExecuteCommandCommand::COMMANDS,
      ),
    );

  <<__Override>>
  public async function executeAsync(
    self::TParams $p,
  ): Awaitable<this::TExecuteResult> {
    $options = TypeAssert\matches_type_structure(
      type_structure(self::class, 'TInitializationOptions'),
      $p['initializationOptions'] ?? shape(),
    );

    $lint_mode = $options['lintMode'] ?? null;

    if ($lint_mode !== null) {
      $this->state->lintMode = $lint_mode;
    }

    invariant($this->state->config === null, 'Tried to set config twice');
    $uri = $p['rootUri'];
    if ($uri === null) {
      $uri = 'file://'.\getcwd();
    }
    if (Str\starts_with($uri, 'file://')) {
      $path = Str\strip_prefix($uri, 'file://');
      $this->state->config = LintRunConfig::getForPath($path);
    }

    return await parent::executeAsync($p);
  }
}
