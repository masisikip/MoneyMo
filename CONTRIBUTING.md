# CONTRIBUTION GUIDELINES

Please follow these guidelines to ensure smooth collaboration and maintain high-quality documentation and code.

## General Guidelines
- **Write documentation** for any new feature or change you implement. This includes:
  - Updating the **README.md** if the overall workflow or instructions change.
  - Adding to the **API documentation** if the backend endpoints are modified.
  - Including relevant **UI documentation** if the user interface is updated or altered.
  - Adding **code comments** where necessary to explain the logic behind complex or non-obvious code.

- Follow the **existing coding conventions** of the project to maintain consistency.
- Ensure that all **tests** (if applicable) pass before submitting your pull request.
- Make sure to **provide meaningful commit messages** describing what was changed and why.
- **Use conventional commit messages** following the format:
  - **feat**: A new feature
  - **fix**: A bug fix
  - **docs**: Documentation only changes
  - **style**: Code style changes (e.g., formatting, missing semicolons)
  - **refactor**: Code changes that neither fix a bug nor add a feature
  - **perf**: Performance improvements
  - **test**: Adding or modifying tests
  - **Example**: 
    - `[feat] Add user authentication page`
    - `[fix] Resolve bug with disabled button styling`


## Pull Requests (PRs)

When submitting a PR, please follow these steps:

1. **Assign an Assignee**
   - **Assign yourself or another contributor** who will be responsible for managing the PR process. The assignee is typically the person working on the PR or coordinating its review.

2. **Provide a Descriptive Title**
   - Use the following format for your PR title:
     ```
     [TYPE] description
     ```
     - Example: `[feat] Add user authentication page`
     - **TYPE** should be one of:
       - `feat` for a new feature
       - `fix` for a bug fix
       - `docs` for documentation changes
       - `style` for code style changes
       - `refactor` for refactoring code
       - `perf` for performance improvements
       - `test` for adding or modifying tests

3. **Add a Descriptive Description**
   - In your PR description, please include a detailed summary of the changes. Use the following template:
     ```
     ### Description
     - Provide a brief overview of the changes.
     - Explain the problem being solved or the feature being added.
     - Mention any important details or decisions made during development.

     ### Type of Change
     - [ ] Bug fix
     - [ ] New feature
     - [ ] Documentation update
     - [ ] Refactor
     - [ ] Other (please specify)

     ### Related Issues
     - Link to any relevant issues (e.g., Fixes #123).

     ### How to Test
     - Provide instructions on how to test these changes (if applicable).

     ### Checklist
     - [ ] Tests pass
     - [ ] Documentation updated
     - [ ] Code reviewed
     - [ ] Assignee and reviewers set
     ```

4. **Link to a Related Issue**
   - If your PR addresses a specific issue, make sure to **link the PR to the related issue** (e.g., "Fixes #123").
   - Linking to an issue helps track the progress of feature implementation or bug fixes and ensures the work is tied to the corresponding discussion.

5. **Assign At Least One Reviewer**
   - **Assign at least one reviewer** who will review your PR and provide feedback.
   - The reviewer will verify your changes, suggest improvements, and approve the PR once everything looks good.

6. **Add Labels**
   - Add appropriate **labels** to your PR to help categorize it (e.g., `enhancement`, `bug`, `documentation`, `high-priority`, etc.).
   - Labels provide more context and help organize the work in the repository.

7. **Provide Screenshots (if applicable)**
   - If your changes affect the UI, please provide relevant screenshots or GIFs.

8. **Ensure New Features are Covered by Tests**
   - If you added new functionality, please include relevant unit or integration tests for that feature.


### What Not to Do
To help streamline the process and maintain a clean codebase, please avoid the following:

- **Do not create large PRs**: Split your work into smaller, manageable PRs to make reviewing easier.
- **Do not mix refactoring and new features**: Separate refactoring changes from new feature implementations in different PRs.
- **Do not submit WIP (Work In Progress) PRs**: Make sure your PR is in a reviewable state before submitting.
- **Do not ignore feedback**: Address comments and suggestions provided by reviewers promptly.
- **Do not make unrelated changes**: Keep your PR focused on a specific feature or bug fix and avoid making unrelated changes in the same PR.


## Review and Approval Process:

1. **Initial Review**:
   - Once the PR is submitted, reviewers will check the changes for correctness, adherence to guidelines, and potential improvements.
   - Reviewers will leave comments and suggest changes if needed.

2. **Addressing Feedback**:
   - If feedback is provided, the **assignee** should address the suggestions and update the PR accordingly.
   - If the reviewer asks for additional changes, make sure they are implemented before asking for approval.

3. **Approval and Merging**:
   - Once the reviewer is satisfied with the changes, they will **approve the PR and merge it**.
   - The **reviewer** is responsible for merging the PR once all checks have passed and all feedback has been addressed.

4. **Merge Strategy**:
   - We use **Squash and Merge** to keep the commit history clean and avoid excessive merge commits.
