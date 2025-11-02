import { useState, useEffect } from "react";
import { NewsArticle, apiService } from "@/services/api";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Label } from "@/components/ui/label";
import { Card } from "@/components/ui/card";
import { X, Upload, Image as ImageIcon } from "lucide-react";

interface NewsFormProps {
  article?: NewsArticle | null;
  onSubmit: (article: NewsArticle) => void;
  onCancel: () => void;
  isLoading: boolean;
}

export const NewsForm = ({ article, onSubmit, onCancel, isLoading }: NewsFormProps) => {
  const [title, setTitle] = useState("");
  const [body, setBody] = useState("");
  const [image, setImage] = useState<File | string | null>(null);
  const [imagePreview, setImagePreview] = useState<string | null>(null);

  useEffect(() => {
    if (article) {
      setTitle(article.title);
      setBody(article.body);
      setImage(article.image);
      if (typeof article.image === 'string') {
        setImagePreview(apiService.getImageUrl(article.image));
      }
    }
  }, [article]);

  const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      setImage(file);
      const reader = new FileReader();
      reader.onloadend = () => {
        setImagePreview(reader.result as string);
      };
      reader.readAsDataURL(file);
    }
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onSubmit({
      id: article?.id,
      title,
      body,
      image,
    });
  };

  const handleRemoveImage = () => {
    setImage(null);
    setImagePreview(null);
  };

  return (
    <Card className="p-6 border-border bg-card">
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-2xl font-bold">
          {article ? 'Edit News Article' : 'Create News Article'}
        </h2>
        <Button variant="ghost" size="icon" onClick={onCancel}>
          <X className="h-5 w-5" />
        </Button>
      </div>

      <form onSubmit={handleSubmit} className="space-y-6">
        <div className="space-y-2">
          <Label htmlFor="title">Title</Label>
          <Input
            id="title"
            value={title}
            onChange={(e) => setTitle(e.target.value)}
            placeholder="Enter article title"
            required
            className="bg-background border-input"
          />
        </div>

        <div className="space-y-2">
          <Label htmlFor="body">Body</Label>
          <Textarea
            id="body"
            value={body}
            onChange={(e) => setBody(e.target.value)}
            placeholder="Enter article content"
            required
            rows={8}
            className="bg-background border-input resize-none"
          />
        </div>

        <div className="space-y-2">
          <Label htmlFor="image">Image</Label>
          
          {imagePreview ? (
            <div className="relative">
              <img
                src={imagePreview}
                alt="Preview"
                className="w-full h-64 object-cover rounded-lg border border-border"
              />
              <Button
                type="button"
                variant="destructive"
                size="icon"
                className="absolute top-2 right-2"
                onClick={handleRemoveImage}
              >
                <X className="h-4 w-4" />
              </Button>
            </div>
          ) : (
            <div className="border-2 border-dashed border-input rounded-lg p-8 text-center hover:border-primary/50 transition-colors">
              <label htmlFor="image" className="cursor-pointer">
                <div className="flex flex-col items-center gap-2">
                  <div className="w-16 h-16 rounded-full bg-muted flex items-center justify-center">
                    <ImageIcon className="w-8 h-8 text-muted-foreground" />
                  </div>
                  <div>
                    <p className="text-sm font-medium">Click to upload image</p>
                    <p className="text-xs text-muted-foreground">PNG, JPG up to 10MB</p>
                  </div>
                  <Button type="button" size="sm" className="mt-2">
                    <Upload className="w-4 h-4 mr-2" />
                    Select File
                  </Button>
                </div>
                <input
                  id="image"
                  type="file"
                  accept="image/*"
                  onChange={handleImageChange}
                  className="hidden"
                />
              </label>
            </div>
          )}
        </div>

        <div className="flex justify-end gap-3 pt-4">
          <Button type="button" variant="outline" onClick={onCancel}>
            Cancel
          </Button>
          <Button type="submit" disabled={isLoading}>
            {isLoading ? 'Saving...' : article ? 'Update Article' : 'Create Article'}
          </Button>
        </div>
      </form>
    </Card>
  );
};
