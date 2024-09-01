## Known issues

### Workarounds listed below

Some known issues have workarounds, that could be common for multiple plugin users. If you encounter one of them, try
one of proposed solutions.

### Issue not listed below

If your problem is not solved below, feel free to report bugs by the **Issues** section on GitHub repository. Each
solution will be added to this section.

- - - -

### Map doesn't load in checkout

The inpost shipping methods have to have "inpost" or "inpost_point" code. The "inpost_point" stands for the package lockers. If you set it up, the map will appear.

### The package locker doesn't store to the order

You probably have misconfigured doctrine. During installation there is a section related to adding association fields to `Order` and `ShippingMethod` endities. Your doctrine configuration type is probably different than the method you used to put them to the entities.

### Pagerfanta error when executing sylius:theme:assets:install --symlink ###

`Failed to copy "vendor/babdev/pagerfanta-bundle/Resources/public/css/pagerfantaDefault.css" because file does not exist`
is issue caused by circular reference between `pagerfantaDefault.css` and it's link in `public` directory.

#### Workaround #### 

* Use both commands `assets:install` and `sylius:theme:assets:install` without `--symlink` option

### Sandbox - Select inPost point ###
* When you place an order in the test environment, make sure that you select a parcel collection point from the map of the Sandbox. You can find the map of available collection points in the test environment at https://sandbox-manager.paczkomaty.pl. Selecting a point that doesn't exist on the test map may result in errors.
