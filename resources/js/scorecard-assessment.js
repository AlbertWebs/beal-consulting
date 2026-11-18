const assessmentForm = document.querySelector('[data-assessment-form]');

if (assessmentForm instanceof HTMLFormElement) {
    const steps = Array.from(assessmentForm.querySelectorAll('[data-assessment-step]'));
    const previousButton = assessmentForm.querySelector('[data-assessment-previous]');
    const nextButton = assessmentForm.querySelector('[data-assessment-next]');
    const submitButton = assessmentForm.querySelector('[data-assessment-submit]');
    const stepLabel = document.querySelector('[data-assessment-step-label]');
    const progressText = document.querySelector('[data-assessment-progress-text]');
    const progressBar = document.querySelector('[data-assessment-progress-bar]');
    const feedback = assessmentForm.querySelector('[data-assessment-feedback]');
    const stepNames = ['Project details', 'Project health today', 'Your contact details'];
    let currentStep = Math.max(steps.findIndex((step) => Array.from(step.querySelectorAll('[data-assessment-error]')).some((error) => error.textContent.trim() !== '')), 0);

    const showStep = () => {
        steps.forEach((step, index) => {
            step.hidden = index !== currentStep;
            step.disabled = index !== currentStep;
        });

        const progress = Math.round(((currentStep + 1) / steps.length) * 100);
        stepLabel.textContent = `Step ${currentStep + 1} of ${steps.length} · ${stepNames[currentStep]}`;
        progressText.textContent = `${progress}%`;
        progressBar.style.width = `${progress}%`;
        previousButton.hidden = currentStep === 0;
        nextButton.hidden = currentStep === steps.length - 1;
        submitButton.hidden = currentStep !== steps.length - 1;
    };

    const clearError = (fieldName) => {
        const error = assessmentForm.querySelector(`[data-assessment-error="${fieldName}"]`);
        const field = assessmentForm.querySelector(`[data-assessment-field="${fieldName}"]`);

        if (error) {
            error.textContent = '';
        }

        field?.querySelectorAll('[aria-invalid="true"]').forEach((input) => input.removeAttribute('aria-invalid'));
    };

    const validateCurrentStep = () => {
        const invalidField = Array.from(steps[currentStep].querySelectorAll('input, select, textarea'))
            .find((field) => !field.checkValidity());

        if (invalidField) {
            invalidField.reportValidity();
            return false;
        }

        if (currentStep === 1 && !steps[currentStep].querySelector('input[name="concerns[]"]:checked')) {
            const error = assessmentForm.querySelector('[data-assessment-error="concerns"]');
            error.textContent = 'Select at least one assessment area.';
            steps[currentStep].querySelector('input[name="concerns[]"]')?.focus();
            return false;
        }

        return true;
    };

    nextButton.addEventListener('click', () => {
        if (!validateCurrentStep()) {
            return;
        }

        currentStep += 1;
        showStep();
        steps[currentStep].querySelector('input, select, textarea')?.focus();
    });

    previousButton.addEventListener('click', () => {
        currentStep = Math.max(0, currentStep - 1);
        showStep();
        steps[currentStep].querySelector('input, select, textarea')?.focus();
    });

    assessmentForm.addEventListener('input', (event) => {
        const fieldName = event.target.closest('[data-assessment-field]')?.dataset.assessmentField;
        if (fieldName) {
            clearError(fieldName);
        }

        if (fieldName === 'concerns' && assessmentForm.querySelector('input[name="concerns[]"]:checked')) {
            clearError('concerns');
        }
    });

    assessmentForm.addEventListener('change', (event) => {
        const fieldName = event.target.closest('[data-assessment-field]')?.dataset.assessmentField;
        if (fieldName) {
            clearError(fieldName);
        }

        if (fieldName === 'concerns' && assessmentForm.querySelector('input[name="concerns[]"]:checked')) {
            clearError('concerns');
        }
    });

    assessmentForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!validateCurrentStep()) {
            return;
        }

        if (currentStep < steps.length - 1) {
            currentStep += 1;
            showStep();
            steps[currentStep].querySelector('input, select, textarea')?.focus();
            return;
        }

        feedback.textContent = 'Sending your assessment request…';
        feedback.removeAttribute('data-state');
        submitButton.disabled = true;
        assessmentForm.setAttribute('aria-busy', 'true');
        steps.forEach((step) => { step.disabled = false; });

        try {
            const response = await fetch(assessmentForm.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(assessmentForm),
            });
            const result = await response.json();

            if (response.status === 422 && result.errors) {
                const firstError = Object.entries(result.errors)[0];

                Object.entries(result.errors).forEach(([errorName, messages]) => {
                    const fieldName = errorName.split('.')[0];
                    const field = assessmentForm.querySelector(`[data-assessment-field="${fieldName}"]`);
                    const error = assessmentForm.querySelector(`[data-assessment-error="${fieldName}"]`);

                    field?.querySelectorAll('input, select, textarea').forEach((input) => input.setAttribute('aria-invalid', 'true'));
                    if (error) {
                        error.textContent = messages[0];
                    }
                });

                const firstErrorFieldName = firstError?.[0].split('.')[0];
                const firstErrorField = firstErrorFieldName
                    ? assessmentForm.querySelector(`[data-assessment-field="${firstErrorFieldName}"]`)
                    : null;
                const errorStep = firstErrorField?.closest('[data-assessment-step]');
                if (errorStep) {
                    currentStep = steps.indexOf(errorStep);
                }
                showStep();
                feedback.dataset.state = 'error';
                feedback.textContent = 'Please review the highlighted fields and try again.';
                assessmentForm.querySelector('[aria-invalid="true"]')?.focus();
                return;
            }

            if (!response.ok) {
                throw new Error(result.message ?? 'We could not send your request right now. Please try again.');
            }

            feedback.dataset.state = 'success';
            feedback.textContent = result.message;
            submitButton.disabled = true;
        } catch (error) {
            feedback.dataset.state = 'error';
            feedback.textContent = error instanceof Error
                ? error.message
                : 'We could not send your request right now. Please try again.';
            submitButton.disabled = false;
        } finally {
            assessmentForm.removeAttribute('aria-busy');
        }
    });

    showStep();
}
