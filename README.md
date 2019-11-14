# Nova Tabs Translations


Nova card to add options to your website. To store data [akaunting/setting](https://github.com/akaunting/setting) is used. 

You can attach the card to any [Nova Dashboard](https://nova.laravel.com/docs/2.0/customization/dashboards.html#default-dashboard). Can be used in multiple dashboard or in any resource.

The information is displayed in tabs.

If you want to use a KeyValue field use `resolveUsing() method to format the values:

```php
KeyValue::make('Meta')->resolveUsing(function ($value) {
	return json_decode($value);
})
```

![cover](https://user-images.githubusercontent.com/74367/68877274-17e05f00-0706-11ea-9690-2485ba896c41.png)


## Instructions

1. Install Package
	```php
	composer require ericlagarda/nova-settings-card
	```


2. Add SettingsCard to your own Nova Dashboard

	```php
	use EricLagarda\SettingsCard\SettingsCard;


	/**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
	    return [
	        (new SettingsCard)->fields([
	            'General' => [
	                Text::make('Web Name'),
	                Boolean::make('Activated'),
	                Trix::make('Site Description'),
	                Image::make('Logo')->disk('s3'),
	                KeyValue::make('Meta')->resolveUsing(function ($value) {
	                    return json_decode($value);
	                }),
	            ],
	            'Scripts' => [
	                Code::make('Header Scripts')->language('javascript'),
	                Code::make('Footer Scripts')->language('javascript'),
	            ],
	            'Styles' => [
	                Code::make('Header Styles')->language('sass'),
	                Code::make('Footer Styles')->language('sass'),
	            ],
	        ]),
	    ];

        ...
    }
    ```
