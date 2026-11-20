// Button with scrollTo action, should have "data-target" attribute with ID of a target element
const clickable_elements = document.querySelectorAll('.buttonScrollTo');

if (clickable_elements) {
    for (let clickable_element of clickable_elements) {
        clickable_element.addEventListener('click', function() {
            let target = clickable_element.dataset ? clickable_element.dataset.target : null;
            if (target) {
                window.scrollTo({top: 0, left: 0, behavior: 'smooth'});
                const target_element = document.getElementById(target);
                if (target_element) {
                    target_element.focus();
                }
            }
        });
    }
}
