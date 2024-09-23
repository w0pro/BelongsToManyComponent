# BelongsToManyComponent

1. A component for interacting with many-through-many relationships through the admin panel
![Image 1](https://github.com/w0pro/BelongsToManyComponent/blob/c23c82cf9019779d788d1fefcb6025b0059a3bec/%D0%A1%D0%BD%D0%B8%D0%BC%D0%BE%D0%BA%20%D1%8D%D0%BA%D1%80%D0%B0%D0%BD%D0%B0%202024-09-23%20%D0%B2%2011.52.38.png)
2. Create a pivot model with migration following the ModelaModelb convention
3. The foreign keys in the pivot model must be within the naming convention and correspond to the format modela_id and modelb_id
4. In order for the link search to work, it is necessary to hang the search trace from Laravel Scout in the subject model
![Image 2](https://github.com/w0pro/BelongsToManyComponent/blob/c23c82cf9019779d788d1fefcb6025b0059a3bec/%D0%A1%D0%BD%D0%B8%D0%BC%D0%BE%D0%BA%20%D1%8D%D0%BA%D1%80%D0%B0%D0%BD%D0%B0%202024-09-23%20%D0%B2%2011.59.32.png)
