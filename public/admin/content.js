function slugify(str) {
	if (str) {	
		// generate random 5-char alphanumeric string
		const randomStr = Math.random().toString(36).substring(2, 7);

		return randomStr + '-' + str
			.normalize('NFD')
			.replace(/[\u0300-\u036f]/g, '') // Remove accents
			.replace(/([^\w]+|\s+)/g, '-')   // Replace space and other characters by hyphen
			.replace(/\-\-+/g, '-')          // Replaces multiple hyphens by one hyphen
			.replace(/(^-+|-+$)/g, '')       // Remove extra hyphens from beginning or end
			.toLowerCase();
	}
	return str;
}

/*
document.addEventListener("content.slug", (e) => {
	e.detail.slug = transliterateSlugify(transliterate(e.detail.text));
});
*/
