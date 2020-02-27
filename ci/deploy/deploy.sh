#! /bin/bash

# Git create or checkout branch shortcut
function switch() {
    git checkout $1 2>/dev/null || git checkout -b $1;
}

# Starting from the last successfully built commit hash, check out
# that hash, do the whole thing, rinse and repeat until we've done all the
# commits.

# Set up global git config
git config --global user.email "nick.vanpraet@dropsolid.com"
git config --global user.name "Nick Vanpraet"

# Setup the public repo
echo "Setting up public repo"
cd /builds/hostingplatform/
git clone https://nick.vanpraet:$GITLAB_PUSH_TOKEN@gitlab.com/dropsolid/rocketship.git public-skeleton

# Start looping
cd /builds/hostingplatform/skeletons-skeleton8
echo "Start looping"
echo "-----------------------------------"

# If $CI_COMMIT_BEFORE_SHA is 00000000000 then just grab last commit
# Presumably this is the first test on a new branch or after gitlab has lost
# track of stuff.
commits=$(git rev-parse --verify HEAD)
if [ "$CI_COMMIT_BEFORE_SHA" != "0000000000000000000000000000000000000000" ] ; then
    commits=$(git rev-list --reverse $CI_COMMIT_SHA...$CI_COMMIT_BEFORE_SHA)
fi

for commit in ${commits}
do
    echo "Checking out $commit"
    git checkout $commit
    # Grab commit info
    AUTHOR_NAME=$(git log -1 --pretty=format:'%an')
    AUTHOR_EMAIL=$(git log -1 --pretty=format:'%ae')
    COMMIT_MESSAGE=$(git log -1 --format=%s)
    # Check if this commit happened on a supported branch
    echo "Checking if this commit happened on a supported branch."
    if ! [[ "$COMMIT_MESSAGE" =~ ^\[\((8\.[0-9]+\.x)\)\].*$ ]] ; then
        # This commit was *not* made on one of the supported branches
        echo "Did not match any supported branches. Entering next loop."
        continue
    fi
    branch="${BASH_REMATCH[1]}"
    echo "We are a go for this commit, $branch is supported"

    echo "Checking if this commit has a tag"
    if git describe --tags --exact-match $commit 2> /dev/null ; then
        tag=$(git describe --tags --exact-match $commit)
        echo "Tag found: $tag"
    else
        unset tag
        echo "No tag found"
    fi

    # Go to other repo, checkout head of this branch
    echo "Resetting other repo"
    cd /builds/hostingplatform/public-skeleton
    switch "$branch"
    # Go back to clean
    cd /builds/hostingplatform/skeletons-skeleton8
    # Clean composer files
    echo "Cleaning private packages"
    php ./ci/deploy/clean_composer.php
    # Sync files
    echo "Syncing files"
    cd /builds/hostingplatform/
    rsync -avz --del --exclude 'artifacts' --exclude 'CHANGELOG.md' --exclude 'bash' --exclude '.git' --exclude 'ci' --exclude 'etc/drush' --exclude 'infra.yml' skeletons-skeleton8/ public-skeleton/
    # Explicitly rsync ci/public to ci
    mkdir -p public-skeleton/ci
    rsync -avz --del skeletons-skeleton8/ci/public_ci/ public-skeleton/ci/
    # Move the gitlab yml file to root of project
    mv -f public-skeleton/ci/.gitlab-ci.yml public-skeleton/.gitlab-ci.yml
    cd public-skeleton
    # If something is different, add the message and author to a list
    echo "Checking if there is something different"
    if ! git diff-index --quiet HEAD --; then
        echo "Committing changes: $AUTHOR_NAME <$AUTHOR_EMAIL> - $COMMIT_MESSAGE"
        git add .
        # Commit as author
        GIT_COMMITTER_NAME="$AUTHOR_NAME" GIT_COMMITTER_EMAIL="$AUTHOR_EMAIL" git commit --author="$AUTHOR_NAME <$AUTHOR_EMAIL>" -m "$COMMIT_MESSAGE"
        if ! [ -z "$tag" ] ; then
            echo "Adding tag"
            git tag "$tag"
        fi
        echo "Pushing to $branch"
        git push origin "$branch" --tags
    elif [[ "$tag" ]] ; then
        echo "No changes, but found new tag"
        git tag "$tag"
        echo "Pushing to $branch"
        git push origin "$branch" --tags
    fi
    # Reset git again before checking out the next commit
    echo "Resetting to $commit"
    cd /builds/hostingplatform/skeletons-skeleton8
    git add .
    git reset --hard $commit
    echo "-----------------------------------"
done

echo "Finished the loop."