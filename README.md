# Recmod (Record Modification) Plugin for CakePHP 1.3+

Copyright 2011, ELASTIC Consultants Inc. (http://elasticconsultants.com)

## Features
 * Record.RecordBehavior
 * Record.RecordShell

## Installation

    git clone http://github.com/nojimage/cakephp-recmod.git app/plugins/recmod

or

    git submodule add http://github.com/nojimage/cakephp-recmod.git app/plugins/recmod

## Usage

### Create logging table

    cake recmod create SomeModelName

### Attach Model

in Model append $actsAs

    public $actsAs = array('Recmod.Recmod');


## License

Licensed under The MIT License.
Redistributions of files must retain the above copyright notice.


Copyright 2011, ELASTIC Consultants Inc. (http://elasticconsultants.com)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
