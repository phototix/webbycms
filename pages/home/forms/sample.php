<?php

declare(strict_types=1);

/*
 * Sample form handler.
 *
 * Handles the POST body (pages/home/index.php -> <input name="form" value="sample">).
 * Flash a message, then redirect (a 303 response) back to the page.
 */

$message = (string) ($request->input('send_something') ?? '');

if ($message !== '') {
    flash()->success('Thanks! You sent: ' . $message);
} else {
    flash()->error('Please enter a message before submitting.');
}

return redirect('/');
