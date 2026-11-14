const enquiryDialog = document.querySelector('[data-enquiry-dialog]');
const enquiryForm = document.querySelector('[data-enquiry-form]');

if (enquiryDialog instanceof HTMLDialogElement && enquiryForm instanceof HTMLFormElement) {
    const serviceSelect = enquiryForm.querySelector('[data-enquiry-service-select]');
    const feedback = enquiryForm.querySelector('[data-enquiry-feedback]');
    const submitButton = enquiryForm.querySelector('[data-enquiry-submit]');
    let lastTrigger = null;

    const clearFeedback = () => {
        feedback.textContent = '';
        feedback.removeAttribute('data-state');

        enquiryForm.querySelectorAll('[data-enquiry-error]').forEach((error) => {
            error.textContent = '';
        });

        enquiryForm.querySelectorAll('[aria-invalid="true"]').forEach((field) => {
            field.removeAttribute('aria-invalid');
        });
    };

    document.querySelectorAll('[data-enquiry-open]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            lastTrigger = trigger;
            clearFeedback();
            enquiryForm.reset();

            if (serviceSelect) {
                serviceSelect.value = trigger.dataset.enquiryService ?? '';
            }

            enquiryDialog.showModal();
            enquiryForm.querySelector('[name="name"]')?.focus();
        });
    });

    enquiryDialog.querySelector('[data-enquiry-close]')?.addEventListener('click', () => {
        enquiryDialog.close();
    });

    enquiryDialog.addEventListener('click', (event) => {
        if (event.target === enquiryDialog) {
            enquiryDialog.close();
        }
    });

    enquiryDialog.addEventListener('close', () => {
        lastTrigger?.focus();
    });

    enquiryForm.addEventListener('input', (event) => {
        if (event.target instanceof HTMLElement && event.target.name) {
            const error = enquiryForm.querySelector(`[data-enquiry-error="${event.target.name}"]`);
            error?.replaceChildren();
            event.target.removeAttribute('aria-invalid');
        }
    });

    enquiryForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearFeedback();
        submitButton.disabled = true;
        enquiryForm.setAttribute('aria-busy', 'true');
        feedback.textContent = 'Sending your enquiry…';

        try {
            const response = await fetch(enquiryForm.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(enquiryForm),
            });
            const result = await response.json();

            if (response.status === 422 && result.errors) {
                Object.entries(result.errors).forEach(([fieldName, messages]) => {
                    const field = enquiryForm.elements.namedItem(fieldName);
                    const error = enquiryForm.querySelector(`[data-enquiry-error="${fieldName}"]`);

                    if (field instanceof HTMLElement) {
                        field.setAttribute('aria-invalid', 'true');
                    }

                    if (error) {
                        error.textContent = messages[0];
                    }
                });

                feedback.dataset.state = 'error';
                feedback.textContent = 'Please review the highlighted fields and try again.';
                enquiryForm.querySelector('[aria-invalid="true"]')?.focus();

                return;
            }

            if (!response.ok) {
                throw new Error(result.message ?? 'We could not send your enquiry right now. Please try again later.');
            }

            enquiryForm.reset();
            feedback.dataset.state = 'success';
            feedback.textContent = result.message;
        } catch (error) {
            feedback.dataset.state = 'error';
            feedback.textContent = error instanceof Error
                ? error.message
                : 'We could not send your enquiry right now. Please try again later.';
        } finally {
            submitButton.disabled = false;
            enquiryForm.removeAttribute('aria-busy');
        }
    });
}
