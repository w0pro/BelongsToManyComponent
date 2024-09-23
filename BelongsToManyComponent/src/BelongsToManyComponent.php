<?php

namespace Wopro\BelongsToManyComponent;

use Laravel\Nova\ResourceTool;

class BelongsToManyComponent extends ResourceTool
{
    /**
     * Get the displayable name of the resource tool.
     *
     * @return string
     */
    public function name()
    {
        return 'Belongs To Many Component';
    }

    /**
     * Get the component name for the resource tool.
     *
     * @return string
     */
    public function component()
    {
        return 'belongs-to-many-component';
    }
}
