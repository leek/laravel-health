<?php

use Spatie\Health\Checks\Checks\OctaneCheck;
use Spatie\Health\Enums\Status;

it('will fail when octane is not installed', function () {
    $result = OctaneCheck::new()->run();

    expect($result->status)->toBe(Status::failed());
});
