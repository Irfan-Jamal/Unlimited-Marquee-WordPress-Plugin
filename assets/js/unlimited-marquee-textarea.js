document.addEventListener('DOMContentLoaded', function () {
    const addTextButton = document.getElementById('add-text');
    const textAreasContainer = document.getElementById('text-areas-container');

    addTextButton.addEventListener('click', function () {
        const count = textAreasContainer.querySelectorAll('.border-gray-300').length; // Adjusted selector to count containers
        const newTextArea = `
            <div class="border border-gray-300 p-4 rounded-lg">
                <label for="text_${count}" class="block mb-2">Text ${count + 1}:</label>
                <textarea name="text_areas[]" id="text_${count}" rows="4" cols="50" class="block w-full border border-gray-300 rounded-md"></textarea>
                <label for="link_${count}" class="block mb-2 mt-4">Link:</label>
                <input type="text" name="text_links[]" id="link_${count}" value="" class="block w-full border border-gray-300 rounded-md">
                <select name="link_targets[]" class="block w-full border border-gray-300 rounded-md mt-4">
                    <option value="_self">Open in Same Tab</option>
                    <option value="_blank">Open in New Tab</option>
                </select>
                <button type="button" class="remove-text bg-red-500 text-white px-4 py-2 mt-4 rounded-lg hover:bg-red-600">Remove</button>
            </div>`;
        textAreasContainer.insertAdjacentHTML('beforeend', newTextArea);
    });

    textAreasContainer.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-text')) {
            e.target.parentElement.remove();
        }
    });
});
