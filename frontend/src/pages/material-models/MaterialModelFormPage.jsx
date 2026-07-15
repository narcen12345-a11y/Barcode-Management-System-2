import { useNavigate, useParams } from 'react-router-dom';
import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useFormSubmit } from '../../hooks/useFormSubmit';
import { materialModelService } from '../../services/materialModelService';
import { materialTypeService } from '../../services/materialTypeService';
import { PageHeader } from '../../components/ui/PageHeader';
import { Spinner } from '../../components/ui/Spinner';

const schema = z.object({
  material_type_id: z.string().min(1, 'Material Type wajib dipilih'),
  name: z.string().min(1, 'Nama wajib diisi').max(100, 'Maksimal 100 karakter'),
  description: z.string().optional().or(z.literal('')),
  is_active: z.boolean().optional().default(true),
});

export function MaterialModelFormPage() {
  const { id } = useParams();
  const isEdit = !!id;
  const navigate = useNavigate();
  const [loadingData, setLoadingData] = useState(isEdit);
  const [materialTypes, setMaterialTypes] = useState([]);
  const [loadingTypes, setLoadingTypes] = useState(true);

  const form = useFormSubmit({
    service: materialModelService,
    method: isEdit ? 'update' : 'create',
    id,
    queryKey: 'material-models',
    onSuccess: () => navigate('/material-models'),
  });

  const {
    register,
    handleSubmit,
    formState: { errors },
    reset,
  } = useForm({
    resolver: zodResolver(schema),
    defaultValues: {
      is_active: true,
    },
  });

  // Load material types for dropdown
  useEffect(() => {
    materialTypeService
      .getAllUnpaginated()
      .then((res) => {
        const items = res.data || res || [];
        setMaterialTypes(items);
      })
      .finally(() => setLoadingTypes(false));
  }, []);

  // Load existing data for edit
  useEffect(() => {
    if (isEdit) {
      materialModelService
        .getById(id)
        .then((res) => {
          const data = res.data || res;
          reset({
            material_type_id: String(data.material_type_id || ''),
            name: data.name || '',
            description: data.description || '',
            is_active: data.is_active ?? true,
          });
        })
        .finally(() => setLoadingData(false));
    }
  }, [id, isEdit, reset]);

  const onSubmit = async (data) => {
    await form.submit({
      ...data,
      material_type_id: Number(data.material_type_id),
    });
  };

  if (loadingData) {
    return (
      <div className="flex items-center justify-center py-20">
        <Spinner size="lg" />
      </div>
    );
  }

  return (
    <div>
      <PageHeader
        title={isEdit ? 'Edit Material Model' : 'Tambah Material Model'}
        description={isEdit ? 'Perbarui data model material' : 'Buat model material baru'}
      />

      <form onSubmit={handleSubmit(onSubmit)} className="max-w-2xl space-y-5">
        {/* Material Type */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">
            Material Type <span className="text-red-400">*</span>
          </label>
          {loadingTypes ? (
            <div className="flex items-center gap-2 text-sm text-slate-500">
              <Spinner size="sm" /> Memuat tipe material...
            </div>
          ) : (
            <select
              {...register('material_type_id')}
              className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
            >
              <option value="">Pilih Material Type</option>
              {materialTypes.map((mt) => (
                <option key={mt.id} value={mt.id}>
                  {mt.name}
                </option>
              ))}
            </select>
          )}
          {errors.material_type_id && (
            <p className="mt-1 text-xs text-red-400">{errors.material_type_id.message}</p>
          )}
          {form.errors?.material_type_id && (
            <p className="mt-1 text-xs text-red-400">{form.errors.material_type_id}</p>
          )}
        </div>

        {/* Name */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">
            Name <span className="text-red-400">*</span>
          </label>
          <input
            {...register('name')}
            placeholder="Nama model material"
            className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
          />
          {errors.name && (
            <p className="mt-1 text-xs text-red-400">{errors.name.message}</p>
          )}
          {form.errors?.name && (
            <p className="mt-1 text-xs text-red-400">{form.errors.name}</p>
          )}
        </div>

        {/* Description */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">Description</label>
          <textarea
            {...register('description')}
            rows={3}
            placeholder="Deskripsi model material"
            className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
          />
          {errors.description && (
            <p className="mt-1 text-xs text-red-400">{errors.description.message}</p>
          )}
          {form.errors?.description && (
            <p className="mt-1 text-xs text-red-400">{form.errors.description}</p>
          )}
        </div>

        {/* Is Active */}
        <div>
          <label className="flex items-center gap-3">
            <input
              type="checkbox"
              {...register('is_active')}
              className="h-4 w-4 rounded border-slate-700 bg-slate-800 text-cyan-500 focus:ring-cyan-500"
            />
            <span className="text-sm font-medium text-slate-300">Active</span>
          </label>
        </div>

        {/* Actions */}
        <div className="flex items-center gap-3 pt-2">
          <button
            type="submit"
            disabled={form.loading}
            className="inline-flex items-center gap-2 rounded-lg bg-cyan-500 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-cyan-600 disabled:cursor-not-allowed disabled:opacity-50"
          >
            {form.loading ? (
              <>
                <Spinner size="sm" />
                Menyimpan...
              </>
            ) : (
              'Simpan'
            )}
          </button>
          <button
            type="button"
            onClick={() => navigate('/material-models')}
            className="rounded-lg border border-slate-700 px-6 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-slate-800"
          >
            Batal
          </button>
        </div>
      </form>
    </div>
  );
}
