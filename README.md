# Expressive Standard Library
<small><center>Because multiple discreet modules are hard</center></small>

## How to install
Add the custom repository 
```json
"repositories": [
        {
            "type": "vcs",
            "url":  "git@github.com:FlexShopper/expressive-stdlib.git"
        }
]
```
Then add it to your `require` block. 
```json
"require": {
	"flexshopper/expressive-stdlib": "^0.1"
}
```

## What's in the library?
1. Application Console
2. PSR7 Request Validators
3. Generic Background Worker
4. Helper functions