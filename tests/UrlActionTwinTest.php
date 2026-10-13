<?php

use Filament\Actions\Action;
use Leek\FilamentRightClick\Menu\ContextMenuItem;

/*
 * A row action with a URL renders as a link in the row; its menu twin must
 * follow that link (same tab, or a new one when the action says so) instead
 * of mounting the action — mounting an EditAction builds a modal form around
 * the whole resource, mounting a URL-only action does nothing at all.
 */
it('tells the browser when a URL action opens in a new tab', function (): void {
    $newTab = ContextMenuItem::for(Action::make('open')->url('https://example.com', shouldOpenInNewTab: true));
    $sameTab = ContextMenuItem::for(Action::make('edit')->url('https://example.com'));
    $plain = ContextMenuItem::for(Action::make('delete')->action(fn () => null));

    expect($newTab->toPayload())->toHaveKey('openUrlInNewTab', true)
        ->and($sameTab->toPayload())->not->toHaveKey('openUrlInNewTab')
        ->and($plain->toPayload())->not->toHaveKey('openUrlInNewTab');
});

it('follows the row link of a URL action instead of mounting it', function (): void {
    $script = file_get_contents(__DIR__ . '/../resources/js/filament-right-click.js');

    expect($script)
        ->toContain('function rowActionLink(row, action)')
        ->toContain("const link = rowActionLink(target, item.mirrors || item.action);")
        ->toContain('followLink(link, item.openUrlInNewTab === true);')
        ->toContain("window.open(link.href, '_blank', 'noopener');")
        ->toContain('link.click();');
});
