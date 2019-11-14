# Files in commit (range)

Github Action that outputs a CSV list with files changed in the given commit (range).

## Options

This action comes with two modes. You either pass no inputs and it gets the changed file list from the `GITHUB_SHA` 
environment variable. Or you pass it the `baseSha` and `headSha` inputs and it will give you the difference between 
them. Not that only passing in either  `baseSha` or `headSha` results in falling back to listing the changed files in 
`GITHUB_SHA`.

### baseSha

The SHA of the base commit.

* *Required*: `Yes`
* *Type*: `string`
* *Example*: `ce28b7e31f089ce537cdec0ae660a74ccb17230f`

### headSha

The SHA of the head commit.

* *Required*: `Yes`
* *Type*: `string`
* *Example*: `a853c03c1b013aa58deee9a6ff43b7a897f6fab8`

## Output

This action has only one output, namely the `files` output. It will container a CSV list of files that have changed in 
the given commit range. For examples:
* `` - Empty, nothing changed.
* `composer.lock` - Only one file changed.
* `composer.json,composer.lock` - Multiple files changed

## License ##

Copyright 2019 [Cees-Jan Kiewiet](http://wyrihaximus.net/)

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
