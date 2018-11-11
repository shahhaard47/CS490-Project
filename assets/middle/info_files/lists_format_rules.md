##LISTS

###RULES FOR CREATING TEST CASES WITH LIST OUTPUT AND INPUT VALUES
##### Formats supported (this could go in the text box in the form of add_question)
- [It,and,a,ago,in,a,a,of,Annabel,and,other,and]
- [“It”,“and”,“a”,“ago”,“in”,“a”,“a”,“of”,“Annabel”,“and”,“other”,“and”]
- [1,2,3,4,5,6,7,8,9]
- [1.3,1.2,3.4]
##### Rules
- All elements must be same type (as selected with the drop down)
- List of strings 
    - No quotes or double quotes (no single quotes)
- No space between elements
##### Implementation
- Keep doing the same thing
- It looks like this after accepted (some things on front might need to be changed)


#### WORKS
look at the first element of array:
```bash
haardshah~/developement/CS490-Project/assets/middle$ php testGrading.php 
-----------Grading test Data-----------
array(3) {
  [0]=>
  array(4) {
    ["qScore"]=>
    float(6)
    ["testCasesPassFail"]=>
    array(5) {
      [0]=>
      int(0)
      [1]=>
      int(0)
      [2]=>
      int(0)
      [3]=>
      int(1)
      [4]=>
      int(1)
    }
    ["comments"]=>
    array(6) {
      [0]=>
      string(21) "Testcases passed: 2/5"
      [1]=>
      string(202) "Failed testcase1: input(str It was in this apartment also that there stood against the western wall a gigantic clock of ebony), expected_output(str [It,in,apartment,also,against,a,of,ebony])	[-4 points]"
      [2]=>
      string(316) "Failed testcase2: input(str It was many and many a year ago in a kingdom by the sea that a maiden there lived whom you may know by the name of Annabel Lee and this maiden she lived with no other thought than to love and be loved by me), expected_output(str [It,and,a,ago,in,a,a,of,Annabel,and,other,and])	[-4 points]"
      [3]=>
      string(276) "Failed testcase3: input(str It's impossible to go through life unscathed Nor should you want to By the hurts we accumulate we measure both our follies and our accomplishments), expected_output(str [It's,impossible,unscathed,accumulate,our,and,our,accomplishments])	[-4 points]"
      [4]=>
      string(36) "Used incorrect function header.	[-2]"
      [5]=>
      string(17) "Final score: 6/20"
    }
    ["questionID"]=>
    int(62)
  }
  [1]=>
  array(4) {
    ["qScore"]=>
    float(18)
    ["testCasesPassFail"]=>
    array(2) {
      [0]=>
      int(1)
      [1]=>
      int(1)
    }
    ["comments"]=>
    array(3) {
      [0]=>
      string(21) "Testcases passed: 2/2"
      [1]=>
      string(36) "Used incorrect function header.	[-2]"
      [2]=>
      string(18) "Final score: 18/20"
    }
    ["questionID"]=>
    int(1)
  }
  [2]=>
  array(4) {
    ["qScore"]=>
    float(4.67)
    ["testCasesPassFail"]=>
    array(3) {
      [0]=>
      int(1)
      [1]=>
      int(1)
      [2]=>
      int(0)
    }
    ["comments"]=>
    array(4) {
      [0]=>
      string(21) "Testcases passed: 2/3"
      [1]=>
      string(73) "Failed testcase3: input(float 1.5), expected_output(int 2)	[-3.33 points]"
      [2]=>
      string(36) "Used incorrect function header.	[-2]"
      [3]=>
      string(20) "Final score: 4.67/10"
    }
    ["questionID"]=>
    int(3)
  }
}
```