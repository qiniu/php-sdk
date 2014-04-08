#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys
import os
import re

re_md_gist = re.compile(r"@gist\(([^\)]+)\)")
re_strip = re.compile(r"^\n*(.*)\n\t*$", re.S)
re_indent = re.compile(r"^(\t*)[^\t\s\n\r]")
line_start = r"(?:^|\n)\s*"
re_gist_comment = dict(
	c = dict(
		start = re.compile(r"%s\/\*\s*@gist\s+([\w\-_]+)\s*\*/.*?\n+" % line_start),
		end = re.compile(r"%s\/\*\s*@endgist\s*\*/" % line_start),
	),
	bash = dict(
		start = re.compile(r"%s#\s*@gist\s+([\w\-_]+).*?\n+" % line_start),
		end = re.compile(r"%s#\s*@endgist" % line_start),
	),
	cpp = dict(
		start = re.compile(r"%s//\s*@gist\s+([\w\-_]+).*?\n+" % line_start),
		end = re.compile(r"%s//\s*@endgist" % line_start)
	),
	html = dict(
		start = re.compile(r"%s<!-- @gist\s+([\w\-_]+?) -->.*?\n+" % line_start),
		end = re.compile(r"%s<!-- @endgist -->" % line_start),
	),
)
cpath = sys.path[0]

def openfile(path):
	if not os.path.exists(path):
		return None
	f = open(path, "r")
	body = f.read()
	f.close()
	return body

def get_gist_block(path):
	gists = dict()
	body = openfile(path)
	if body is None:
		return gists
	start = 0
	while True:
		a = search_one_block(body[start:])
		if a is None:
			break
		name, content, new_start = a
		start += new_start
		if not name in gists:
			gists[name] = content
		else:
			gists[name].extend(["", "...", ""])
			gists[name].extend(content)
	gists[""] = body.split("\n")
	return gists

def search_one_block(body):
	if len(body) == 0:
		return None
	for n, regs in re_gist_comment.iteritems():
		a = regs["start"].search(body)
		if a is None:
			continue
		start = a.span()[1]
		b = regs["end"].search(body[start:])
		if b is None:
			continue
		break
	if a is None or b is None:
		return None

	body = body[start: b.span()[0]+start]
	body = re_strip.sub("\\1", body)
	start_indent = len(re_indent.findall(body)[0])
	body = [i[start_indent:] for i in body.split("\n")]
	return a.group(1), body, b.span()[1] + start

def dirname(path):
	name = os.path.dirname(path)
	if name == "":
		name = "."
	return name

if __name__ == "__main__":
	if len(sys.argv) <= 1:
		sys.stderr.write("Usage: %s GistFile > OutputFile\n" % os.path.basename(sys.argv[0]))
		exit(2)

	body = openfile(sys.argv[1])
	if body is None:
		sys.stderr.write("Not such File.")
		exit(2)

	rpath = dirname(sys.argv[1])
	body_gist_ref = []
	ref_files = []
	for i in re_md_gist.findall(body):
		file_path = i
		if i.find("#") > 0:
			file_path = file_path.split("#")[0]
		ref_files.append("%s/%s" % (rpath, file_path))
		body_gist_ref.append(i)
	ref_files = list(set(ref_files))

	match_gists = {}
	for f in ref_files:
		blocks = get_gist_block(f)
		for block_key in blocks:
			key = "%s#%s" % (f, block_key)
			if len(block_key) == 0:
				key = "%s%s" % (f, block_key)
			match_gists[key] = blocks[block_key]

	errors = []
	for i in body_gist_ref:
		key = "%s/%s" % (rpath, i)
		if key in match_gists:
			match_results = re_md_gist.search(body)
			if match_results is None:
				continue
			s = match_results.span()[0]
			s = body[body[s-50: s].rfind("\n")+s-50+1: s]
			content = (("\n%s" % s).join(match_gists[key])).strip()
			content = content.replace("\\", "\\\\")

			body = re.sub(r"@gist\s*\(%s\)" % i, content, body)
		else:
			errors.append(i)

	if len(errors) > 0:
		sys.stderr.write("error: No Such File or Anchor\n")
		for i, error in enumerate(errors):
			sys.stderr.write("%s: '%s'\n" % (i+1, error))
		exit(2)
	print body
