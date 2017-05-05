# MMCmfAdminRoutingAddonBundle

### Append to app/AppKernel.php

```
...
    public function registerBundles()
    {
        ....
        $bundles = array(
            ...
           new MandarinMedien\MMCmf\Admin\PageAddonBundle\MMCmfAdminRoutingAddonBundle(),
           ...
        );
        ....
    }
...
```

### Append to src/MY_CUSTOM_ADMIN_BUNDLE/config/config.yml

```
...

  - { resource: "@MMCmfAdminRoutingAddonBundle/Resources/config/config.yml" }

...
```

### Append to src/MY_CUSTOM_ADMIN_BUNDLE/config/routing.yml

```
...

mm_cmf_routing_addon:
    resource: "@MMCmfAdminRoutingAddonBundle/Resources/config/routing.yml"
    prefix:   /routing

...
```