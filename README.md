### PHPMD Variable Lifetime Rule

## How to use?

```
composer require "ralfmaxxx/phpmd_variable_lifetime"
```

And append this rule to `phpmd.xml` using definition file `rulesets/design.xml`:

```
<rule ref="{path}/rulesets/design.xml/LocalVariableLifetime" />
```

You can additionally set allowed lines interval parameter:

```
<rule ref="{path}/rulesets/design.xml/LocalVariableLifetime">
    <properties>
        <property name="allowedLinesInterval" value="5"/>
    </properties>
</rule>
```
