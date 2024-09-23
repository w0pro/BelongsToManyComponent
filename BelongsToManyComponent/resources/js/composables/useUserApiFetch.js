
export function useUserApiFetch(path, options = {}, method = "get") {
    return Nova.request()[method](`/nova-vendor/belongs-to-many-component/${path}`, options);
}
