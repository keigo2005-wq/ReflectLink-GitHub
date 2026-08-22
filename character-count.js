function setCharacterCount(textareaId, countId) {
    const textarea = document.getElementById(textareaId);
    const counter = document.getElementById(countId);

    if (!textarea || !counter) {
        return;
    }

    function updateCount() {
        counter.textContent = textarea.value.length;
    }

    updateCount();
    textarea.addEventListener("input", updateCount);
}

setCharacterCount("issue", "issue-count");
setCharacterCount("cause", "cause-count");
setCharacterCount("improvement", "improvement-count");