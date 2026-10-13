<?php

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Leek\FilamentRightClick\Menu\ContextMenuItem;

/*
 * Filament's prebuilt actions carry their icon as groupedIcon()/tableIcon()
 * only; the bare getIcon() is null and the menu entries for «Edit», «View»
 * and «Delete» came up iconless. The entry falls back like Filament's own
 * renderer: grouped icon first, table icon next. (Resolved through the
 * protected getter: the test app renders no icon sets.)
 */
function entryIcon(ContextMenuItem $item): mixed
{
    return (new ReflectionMethod($item, 'getIcon'))->invoke($item);
}

it('gives a prebuilt action its grouped or table icon', function (): void {
    expect(entryIcon(ContextMenuItem::for(EditAction::make())))->toBe(Heroicon::PencilSquare)
        ->and(entryIcon(ContextMenuItem::for(ViewAction::make())))->toBe(Heroicon::Eye)
        ->and(entryIcon(ContextMenuItem::for(DeleteAction::make())))->toBe(Heroicon::Trash);
});

it('prefers the grouped icon over the table icon, and an explicit entry icon over both', function (): void {
    $action = Action::make('x')->groupedIcon('heroicon-o-star')->tableIcon('heroicon-o-moon');

    expect(entryIcon(ContextMenuItem::for($action)))->toBe('heroicon-o-star')
        ->and(entryIcon(ContextMenuItem::for($action)->icon('heroicon-o-sun')))->toBe('heroicon-o-sun')
        ->and(entryIcon(ContextMenuItem::for(Action::make('plain'))))->toBeNull();
});
