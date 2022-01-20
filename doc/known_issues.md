## Known issues

### Workarounds listed below

Some known issues have workarounds, that could be common for multiple plugin users. If you encounter one of them, try
one of proposed solutions.

### Issue not listed below

If your problem is not solved below, feel free to report bugs by the **Issues** section on GitHub repository. Each
solution will be added to this section.

- - - -

### Pagerfanta error when executing sylius:theme:assets:install --symlink ###

`Failed to copy "vendor/babdev/pagerfanta-bundle/Resources/public/css/pagerfantaDefault.css" because file does not exist`
is issue caused by circular reference between `pagerfantaDefault.css` and it's link in `public` directory.

#### Workaround #### 

* Use both commands `assets:install` and `sylius:theme:assets:install` without `--symlink` option