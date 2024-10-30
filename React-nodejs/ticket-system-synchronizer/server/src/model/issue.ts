interface Issue {
    id: string;
    key: string;
    fields: {
      summary: string;
      status: {
        name: string;
        statusCategory: {
          colorName: string;
        }
      };
      project?: {
        name: string;
      };
      assignee?: {
        displayName: string;
      };
      reporter?: {
        displayName: string;
      };
    };
  }
