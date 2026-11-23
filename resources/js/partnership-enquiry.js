const partnerForm = document.querySelector('[data-partner-wizard]');

if (partnerForm instanceof HTMLFormElement) {
    const steps = [...partnerForm.querySelectorAll('[data-partner-step]')];
    const progressItems = [...document.querySelectorAll('[data-partner-progress]')];
    const nextButton = partnerForm.querySelector('[data-partner-next]');
    const backButton = partnerForm.querySelector('[data-partner-back]');
    const submitButton = partnerForm.querySelector('[data-partner-submit]');
    const stepCount = partnerForm.querySelector('[data-partner-step-count]');
    const sectorField = partnerForm.querySelector('.partner-sector-field');
    const mathDialog = document.querySelector('[data-partner-math-dialog]');
    const mathAnswer = document.querySelector('[data-partner-math-answer]');
    const confirmButton = document.querySelector('[data-partner-confirm]');
    let currentStep = steps.findIndex((step) => step.querySelector('.partner-field small'));
    let verified = false;

    if (currentStep < 0) {
        currentStep = 0;
    }

    const showStep = (index) => {
        currentStep = index;

        steps.forEach((step, stepIndex) => {
            const isActive = stepIndex === index;
            step.hidden = !isActive;
            step.classList.toggle('is-active', isActive);
        });

        progressItems.forEach((item, itemIndex) => {
            item.classList.toggle('is-active', itemIndex === index);
            item.classList.toggle('is-complete', itemIndex < index);
        });

        backButton.hidden = index === 0;
        nextButton.hidden = index === steps.length - 1;
        submitButton.hidden = index !== steps.length - 1;
        stepCount.textContent = `Step ${index + 1} of ${steps.length}`;
    };

    const validateCurrentStep = () => {
        const invalidField = [...steps[currentStep].querySelectorAll('input, select, textarea')]
            .find((field) => !field.checkValidity());

        if (invalidField) {
            invalidField.reportValidity();
            return false;
        }

        if (currentStep === 1) {
            const hasSector = partnerForm.querySelector('input[name="sectors[]"]:checked');
            const sectorError = sectorField.querySelector('[data-partner-sector-error]');
            sectorField.classList.toggle('has-error', !hasSector);
            sectorError.hidden = Boolean(hasSector);

            if (!hasSector) {
                sectorField.querySelector('input[type="checkbox"]')?.focus();
                return false;
            }
        }

        return true;
    };

    nextButton.addEventListener('click', () => {
        if (validateCurrentStep()) {
            showStep(Math.min(currentStep + 1, steps.length - 1));
            steps[currentStep].querySelector('input, select, textarea')?.focus();
        }
    });

    backButton.addEventListener('click', () => showStep(Math.max(currentStep - 1, 0)));

    partnerForm.addEventListener('submit', (event) => {
        if (verified) {
            return;
        }

        event.preventDefault();

        for (let stepIndex = 0; stepIndex < steps.length; stepIndex += 1) {
            showStep(stepIndex);

            if (!validateCurrentStep()) {
                return;
            }
        }

        if (mathDialog instanceof HTMLDialogElement) {
            mathDialog.showModal();
            mathAnswer?.focus();
        } else {
            partnerForm.requestSubmit();
        }
    });

    mathAnswer?.addEventListener('input', () => mathAnswer.setCustomValidity(''));

    confirmButton?.addEventListener('click', () => {
        if (!(mathAnswer instanceof HTMLInputElement) || !mathAnswer.value.trim()) {
            mathAnswer?.setCustomValidity('Please enter the answer to the sum.');
            mathAnswer?.reportValidity();
            return;
        }

        verified = true;
        mathDialog.close();
        partnerForm.requestSubmit();
    });

    showStep(currentStep);

    if (mathDialog instanceof HTMLDialogElement && mathDialog.hasAttribute('data-math-error')) {
        mathDialog.showModal();
        mathAnswer?.focus();
    }
}
