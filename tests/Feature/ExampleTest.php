<?php

test('unauthenticated root redirects to login page', function () {
    $response = $this->get('/');
    $response->assertRedirect('/login');
});
