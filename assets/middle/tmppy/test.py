def isSubstring(str1,str2):
	if(str1 in str2):
		return True
	else:
		return False
response = isSubstring(str("photo"), str("picture"))
correct = bool(False)
if (response == correct):
	print('output is correct')
else:
	print(response)