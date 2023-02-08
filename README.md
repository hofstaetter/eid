# ID Austria (e-ID)

```bash
composer require hofstaetter/eid
```

## Installation & Basic Usage

Please see the [Base Installation Guide](https://socialiteproviders.com/usage/), then follow the provider specific instructions below.

### Add configuration to `config/services.php`

```php
'eid' => [    
  'client_id' => env('EID_CLIENT_ID'),
  'client_secret' => env('EID_CLIENT_SECRET'),
  'redirect' => env('EID_REDIRECT_URI'),
  'endpoint' => env('EID_ENDPOINT', 'eid.oesterreich.gv.at'),
],
```

### Add provider event listener

Configure the package's listener to listen for `SocialiteWasCalled` events.

Add the event to your `listen[]` array in `app/Providers/EventServiceProvider`. See the [Base Installation Guide](https://socialiteproviders.com/usage/) for detailed instructions.

```php
protected $listen = [
    \SocialiteProviders\Manager\SocialiteWasCalled::class => [
        // ... other providers
        \Hofstaetter\Eid\EidExtendSocialite::class.'@handle',
    ],
];
```

### Usage

You should now be able to use the provider like you would regularly use Socialite (assuming you have the facade installed):

```php
return Socialite::driver('eid')->redirect();
```

Within your callback function:
```php
public function callback(Request $request)
{
    $user = Socialite::driver('eid')->user();
    // handle user and app auth
}
```

### Returned User fields

- ``id``
- ``name``
- ``email``

