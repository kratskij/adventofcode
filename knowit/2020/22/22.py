import re, sys

inputPattern = re.compile("(.*)\s\[(.*)\]")
lineIdx = maxLine = maxx = 0
for line in sys.stdin.readlines():
    #print line
    (letters, names) = inputPattern.search(line).groups()
    names = names.lower().split(", ")
    found = 0
    for name in names:
        if re.findall(".*".join(name), letters):
            pos = 0
            while name is not "":
                pos = pos + letters[pos:].index(name[0])
                name = name[1:]
                letters = letters[:pos] + letters[pos+1:]
            found = found + 1
    if found > maxx:
        maxx = found
        maxLine = lineIdx
    lineIdx = lineIdx + 1
print maxLine, maxx





#for line in lines:
#    re
#name = "henrik"
#string = "isaldhlskalelkdnlksrisdsak"
#if re.findall(".*".join(name), string):
#  print "found"
#else:
#  print "not found"
