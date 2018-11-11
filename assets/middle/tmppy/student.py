def initialVowel(text):
	words=text.split(" ")
	vowels=[]
	for word in words:
		if('aeiou'.find(word[0])!=-1):
			vowels.append(word)
	return vowels