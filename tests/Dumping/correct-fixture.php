<?php

// DUMP_VERSION: 1

return array (
  'Tests\\Fakes\\FooBarDTO' => 
  \Recoded\ObjectHydrator\Hydration\Plan::__set_state(array(
     'initializer' => NULL,
     'parameters' => 
    array (
      0 => 
      \Recoded\ObjectHydrator\Hydration\Parameter::__set_state(array(
         'name' => 'foo',
         'type' => 
        \Recoded\ObjectHydrator\Hydration\ParameterType::__set_state(array(
           'types' => 
          array (
            0 => 'Tests\\Fakes\\BarStringDTO',
          ),
           'nullable' => false,
           'resolver' => NULL,
           'composition' => 
          \Recoded\ObjectHydrator\Hydration\ParameterTypeComposition::Union,
        )),
         'default' => NULL,
         'attributes' => 
        array (
        ),
         'typeMappers' => 
        array (
        ),
      )),
    ),
  )),
);
